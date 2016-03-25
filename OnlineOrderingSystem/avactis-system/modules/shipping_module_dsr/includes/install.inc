<?php

        global $application;
        $tables = Shipping_Module_DSR::getTables();
        $query = new DB_Table_Create($tables);

        $table = 'sm_dsr_settings';                         
        $columns = $tables[$table]['columns'];                           

        $query = new DB_Insert($table);
        $query->addInsertValue("RATE_UNIT", $columns['key']);
        $query->addInsertValue('s:6:"weight";', $columns['value']);
        $application->db->getDB_Result($query);

        # Adding demo Shipping data

        $table = 'sm_dsr_methods';                         
        $columns = $tables[$table]['columns'];
        
        # Methods

        $query = new DB_Insert($table);
        $query->addInsertValue('National Priority Airmail', $columns['method_name']);
        $query->addInsertValue('Y', $columns['available']);
        $query->addInsertValue('L', $columns['destination']);
        $application->db->getDB_Result($query);

        $method_1 = mysql_insert_id();

        $query = new DB_Insert($table);
        $query->addInsertValue('Ground Shipping', $columns['method_name']);
        $query->addInsertValue('Y', $columns['available']);
        $query->addInsertValue('L', $columns['destination']);
        $application->db->getDB_Result($query);
        
        $method_2 = mysql_insert_id(); 

        $query = new DB_Insert($table);
        $query->addInsertValue('Delivery on Next Business Day', $columns['method_name']);
        $query->addInsertValue('Y', $columns['available']);
        $query->addInsertValue('L', $columns['destination']);
        $application->db->getDB_Result($query);

        $method_3 = mysql_insert_id();

        # Rates for each method

        $table = 'sm_dsr_rates';                         
        $columns = $tables[$table]['columns'];

/*-------------------- Method 1 ------------------------*/

        $data[] = array(
            "method_id"     => $method_1 
            ,"country_id"   => '223' // US
            ,"state_id"     => '-1'  // All states
            ,"wrange_from"       => '0'
            ,"wrange_to"        => '2'
            ,"bcharge_abs"      => '10'
            ,"bcharge_perc"     => '2'
            ,"acharge_pi_abs"   => '1'
            ,"acharge_pwu_abs"  => '0'
        );
        
        $data[] = array(
            "method_id"     => $method_1 
            ,"country_id"   => '223' // US
            ,"state_id"     => '-1'  // All states
            ,"wrange_from"       => '2.01'
            ,"wrange_to"        => '4'
            ,"bcharge_abs"      => '10'
            ,"bcharge_perc"     => '2.5'
            ,"acharge_pi_abs"   => '1.5'
            ,"acharge_pwu_abs"  => '0'
        );

        $data[] = array(
            "method_id"     => $method_1 
            ,"country_id"   => '223' // US
            ,"state_id"     => '-1'  // All states
            ,"wrange_from"       => '4.01'
            ,"wrange_to"        => '10'
            ,"bcharge_abs"      => '10'
            ,"bcharge_perc"     => '3.5'
            ,"acharge_pi_abs"   => '2'
            ,"acharge_pwu_abs"  => '0'
        );

/*-------------------- Method 2 ------------------------*/

        $data[] = array(
            "method_id"     => $method_2 
            ,"country_id"   => '223' // US
            ,"state_id"     => '-1'  // All states
            ,"wrange_from"       => '0'
            ,"wrange_to"        => '5'
            ,"bcharge_abs"      => '6.0'
            ,"bcharge_perc"     => '0'
            ,"acharge_pi_abs"   => '1'
            ,"acharge_pwu_abs"  => '0'
        );

        $data[] = array(
            "method_id"     => $method_2 
            ,"country_id"   => '223' // US
            ,"state_id"     => '-1'  // All states
            ,"wrange_from"       => '5.01'
            ,"wrange_to"        => '10'
            ,"bcharge_abs"      => '10'
            ,"bcharge_perc"     => '0'
            ,"acharge_pi_abs"   => '1'
            ,"acharge_pwu_abs"  => '0'
        );

        $data[] = array(
            "method_id"     => $method_2 
            ,"country_id"   => '223' // US
            ,"state_id"     => '-1'  // All states
            ,"wrange_from"       => '10.01'
            ,"wrange_to"        => '10000'
            ,"bcharge_abs"      => '0'
            ,"bcharge_perc"     => '35.0'
            ,"acharge_pi_abs"   => '5'
            ,"acharge_pwu_abs"  => '0'
        );

/*-------------------- Method 3 ------------------------*/

        $data[] = array(
            "method_id"     => $method_3
            ,"country_id"   => '223' // US
            ,"state_id"     => '-1'  // All states
            ,"wrange_from"       => '0'
            ,"wrange_to"        => '1'
            ,"bcharge_abs"      => '35.00'
            ,"bcharge_perc"     => '0'
            ,"acharge_pi_abs"   => '0'
            ,"acharge_pwu_abs"  => '0'
        );

        $data[] = array(
            "method_id"     => $method_3 
            ,"country_id"   => '223' // US
            ,"state_id"     => '-1'  // All states
            ,"wrange_from"       => '1.01'
            ,"wrange_to"        => '3'
            ,"bcharge_abs"      => '45.50'
            ,"bcharge_perc"     => '0'
            ,"acharge_pi_abs"   => '0'
            ,"acharge_pwu_abs"  => '0'
        );

        $data[] = array(
            "method_id"         => $method_3 
            ,"country_id"       => '223' // US
            ,"state_id"         => '-1'  // All states
            ,"wrange_from"      => '3.01'
            ,"wrange_to"        => '10'
            ,"bcharge_abs"      => '65.00'
            ,"bcharge_perc"     => '0'
            ,"acharge_pi_abs"   => '0'
            ,"acharge_pwu_abs"  => '0'
        );

        foreach ($data as $rate_data)
        {

            $query = new DB_Insert('sm_dsr_rates');
            $query->addInsertValue($rate_data["method_id"],$columns['method_id']);
            $query->addInsertValue($rate_data["country_id"],$columns['dst_country']);
            $query->addInsertValue($rate_data["state_id"],$columns['dst_state']);
            $query->addInsertValue($rate_data["wrange_from"],$columns['wrange_from']);
            $query->addInsertValue($rate_data["wrange_to"],$columns['wrange_to']);
            $query->addInsertValue($rate_data["bcharge_abs"],$columns['bcharge_abs']);
            $query->addInsertValue($rate_data["bcharge_perc"], $columns['bcharge_perc']);
            $query->addInsertValue($rate_data["acharge_pi_abs"],$columns['acharge_pi_abs']);
            $query->addInsertValue(0.00, $columns['acharge_pi_perc']);
            $query->addInsertValue($rate_data["acharge_pwu_abs"],$columns['acharge_pwu_abs']);
            $query->addInsertValue(0.00, $columns['acharge_pwu_perc']);
            $application->db->getDB_Result($query);
        }
        
        
?>
