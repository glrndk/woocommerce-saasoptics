<?php
class SaasOptics
{
    /**
     * Map woocommerce products to saasoptics products
     *
     * @param int $woo_product_id
     * @return void
     */
    protected function map_products($woo_product_id)
    {
        switch ($woo_product_id) {
            case 121280:
                return  get_option('gl_saas_optics_yearly');
                break;
            case 109663:
                return  get_option('gl_saas_optics_monthly');
                break;
            default:
                return 'not a subscription';
                break;
        }
    }
    public function init()
    {
        add_action('woocommerce_new_order', [$this, 'syncOrderToSaasOptics'], 10, 2);
    }

    /**
     * Sync orders and customers to SaasOptics, if the customer has no contract create the contract id.
     *
     * @param [type] $order_id
     * @param [type] $order
     * @return void
     */
    public function syncOrderToSaasOptics($order_id, $order)
    {
        $user_id = $order->get_user_id();
        $user_so_contract_id = get_user_meta($user_id, 'gl_so_contract_id', true);
        $api_key = get_option('gl_saas_optics_api_key');
        $api_url = get_option('gl_saas_optics_api_url');
        $date = $order->get_date_created();

        if (empty($user_so_contract_id)) {
            $billing_first_name = $order->get_billing_first_name();
            $billing_last_name = $order->get_billing_last_name();
            $billing_company = $order->get_billing_company();
            $billing_address_1 = $order->get_billing_address_1();
            $billing_address_2 = $order->get_billing_address_2();
            $billing_city = $order->get_billing_city();
            $billing_postcode = $order->get_billing_postcode();
            $billing_country = $order->get_billing_country();
            $billing_phone = $order->get_billing_phone();
            $billing_email = $order->get_billing_email();
            $currency = $order->get_currency();

            $billing_profile = [
                'name' => $billing_first_name . ' ' . $billing_last_name,
                'company_name' => $billing_company,
                "first_name" => $billing_first_name,
                "last_name" => $billing_last_name,
                "addr1" => $billing_address_1,
                "addr2" => $billing_address_2,
                "city" => $billing_city,
                "zip_code" => $billing_postcode,
                "country" => $billing_country,
                "phone" => $billing_phone,
                "email" => $billing_email,
                "is_active" => true,
                //"currency" => $currency,
            ];

            $customer = [
                'name' => (empty($billing_company) ? $billing_first_name . ' ' . $billing_last_name : $billing_company),
                'billing_profile' => $billing_profile,
                "text_field1" => uniqid('kort_', true),
                "is_active" => true,
                "email" => $billing_email,
            ];

            // Create a customer object in SaasOptics
            $client = new SaasOpticsClient($api_key, $api_url);
            $response = $client->post('customers', ['body' => json_encode($customer)]);

            $response = json_decode($response['body']);

            if (empty($response->id)) {
                $this->logAndMail($response);
                wp_mail('it@golearn.dk', 'Problemer med at oprette en bruger i SaasOptics', 'Der har været et problem med at oprette en bruger i SaasOptics på følgende WP ID:' . $user_id . ' klokken ' . current_time('d-m-Y') . '\r\n Response:' . json_encode($response));
                exit;
            }

            $customer_id = $response->id;
            update_user_meta($user_id, 'gl_so_customer_id', $customer_id);

            $contract = [
                'register' => get_option('gl_saas_optics_register', ''),
                'billing_profile' => $billing_profile,
                'customer' => $customer_id,
            ];

            // Create the contract object in SaasOptics related to the customer for invoicing
            $response = $client->post('contracts', ['body' => json_encode($contract)]);
            if ($response['response']['message'] !== 'Created') {
                $this->logAndMail($response);
                wp_mail('it@golearn.dk', 'Problemer med at oprette en kontrakt i SaasOptics', 'Der har været et problem med at oprette en kontrakt i SaasOptics på følgende bruger:' . $customer_id . ' klokken ' . current_time('d-m-Y') . '\r\n Response:' . json_encode($response));
                exit;
            }
            $response = json_decode($response['body']);
            $user_so_contract_id = $response->id;
            update_user_meta($user_id, 'gl_so_contract_id', $user_so_contract_id);
        }

        $line_items = [];
        foreach ($order->get_items() as $item_id => $item) {
            $line_items[] = [
                'local_amount' => $item->get_total(),
                'item' => $this->map_products($item->get_product_id()),
                'quantity' => $item->get_quantity()
            ];
        }
        $invoice = [
            'date' => $date->date('Y-m-d'),
            'contract' => $user_so_contract_id,
            'line_items' => $line_items,
        ];

        // Create the invoice in Saas Optics
        $client = new SaasOpticsClient($api_key, $api_url);
        $response = $client->post('invoices', ['body' => json_encode($invoice)]);

        if ($response['response']['message'] !== 'Created') {
            get_user_meta($user_id, 'gl_so_customer_id', true);
            $this->logAndMail($response);
            wp_mail('it@golearn.dk', 'Problemer med at oprette en faktura i SaasOptics', 'Der har været et problem med at oprette en faktura i SaasOptics på følgende bruger:' . $customer_id . ' klokken ' . current_time('d-m-Y') . '\r\n Response:' . json_encode($response));
            exit;
        }

        return true;
    }

    protected function logAndMail($entry, $mode = 'a', $file = 'gl_woocommerce_saasoptics')
    {
        // Get WordPress uploads directory.
        $upload_dir = wp_upload_dir();
        $upload_dir = $upload_dir['basedir'];
        // If the entry is array, json_encode.
        if (is_array($entry)) {
            $entry = json_encode($entry);
        }
        // Write the log file.
        $file  = $upload_dir . '/' . $file . '.log';
        $file  = fopen($file, $mode);
        $bytes = fwrite($file, current_time('d-m-Y') . "::" . $entry . "\n");
        fclose($file);
        return $bytes;
    }

    protected function debug($debug)
    {
        echo "<pre>";
        var_dump($debug);
        echo "</pre>";
        exit();
    }
}