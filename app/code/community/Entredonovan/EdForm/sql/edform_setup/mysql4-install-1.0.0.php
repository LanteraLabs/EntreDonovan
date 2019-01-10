<?php

$this->startSetup();

	$this->run("

		CREATE TABLE IF NOT EXISTS {$this->getTable('edform_users')} (
		  `user_id` int(11) unsigned NOT NULL auto_increment,
		  `username` varchar(150) NOT NULL default '',
		  `password` varchar(255) NOT NULL default '',
		  `account_name` varchar(255) NOT NULL default '',
		  `contact_firstname` varchar(255) NOT NULL default '',
		  `contact_lastname` varchar(255) NOT NULL default '',
		  `phone` varchar(255) NOT NULL default '',
		  `email` varchar(255) NOT NULL default '',
		  `shipping_firstname` varchar(255) NOT NULL default '',
		  `shipping_lastname` varchar(255) NOT NULL default '',
		  `shipping_street1` varchar(255) NOT NULL default '',
		  `shipping_street2` varchar(255) NULL default NULL,
		  `shipping_city` varchar(255) NOT NULL default '',
		  `shipping_state` varchar(255) NOT NULL default '',
		  `shipping_zip` VARCHAR(10) NULL DEFAULT NULL,
		  `shipping_phone` varchar(255) NOT NULL default '',
		  `shipping_country_id` varchar(4) NOT NULL default '',
		  `shipping_instruction` text NULL DEFAULT NULL,
		  `logo` varchar(255) NOT NULL default '',
		  `logo_type` VARCHAR(32) NULL,
		  `weight_type` VARCHAR(12) NOT NULL DEFAULT 'kg',
		  `ship_type` ENUM('flat','pressed_hanging') NULL DEFAULT NULL,
		  `ship_flat` float(12,4) default 0,
		  `retail_markup` FLOAT(12,4) NULL DEFAULT NULL,
		  `ship_pressed_hanging` varchar(255) NOT NULL default '',
		  `ship_wood_hanger` varchar(16) NOT NULL default '',
		  `ship_garment_bag` varchar(16) NOT NULL default '',
		  `ship_sew_label` varchar(255) NOT NULL default '',
		  `ship_sew_client_monogram_label` varchar(255) NOT NULL default '',
		  `volume_tier_with_ed` text NULL default NULL,
		  `account_type` ENUM('WC','IC','CA') NOT NULL DEFAULT 'WC',
		  `tax_rate` varchar(255) NULL default NULL,
		  `tax_state` VARCHAR(32) NULL DEFAULT NULL,
		  `payment_type` VARCHAR(16) NULL DEFAULT 'Other',
		  `payment_method` varchar(255) NOT NULL default '',
		  `privacy_policy` TEXT NULL DEFAULT NULL,
		  `approved` enum('0','1') default '0',
		  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `reset_token` VARCHAR(64) NULL DEFAULT NULL,
		  `reset_at` TIMESTAMP NULL DEFAULT NULL,
		  `parent_id` INT(11) UNSIGNED NULL DEFAULT NULL, 
		  PRIMARY KEY (`user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		ALTER TABLE {$this->getTable('edform_users')} ADD UNIQUE ( `username`);
		ALTER TABLE {$this->getTable('edform_users')} ADD INDEX(`parent_id`);
		ALTER TABLE {$this->getTable('edform_users')} ADD FOREIGN KEY (`parent_id`) REFERENCES {$this->getTable('edform_users')}(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
		
		
		
		
		CREATE TABLE IF NOT EXISTS {$this->getTable('edform_orders')} (
		  `order_id` bigint(11) NOT NULL,
		  `user_id` int(11) UNSIGNED NOT NULL,
		  `client_name` varchar(255) DEFAULT NULL,
		  `order_date` date NOT NULL,
		  `monogram_name` varchar(255) DEFAULT NULL,
		  `date_of_body_scan` date DEFAULT NULL,
		  `client_weight` varchar(12) DEFAULT NULL,
		  `client_weight_date` date DEFAULT NULL,
		  `order_number` varchar(255) DEFAULT NULL,
		  `ca_po` varchar(255) DEFAULT NULL,
		  `sales_rep` enum('WC','IC','CA') DEFAULT NULL,
		  `sales_rep_name` varchar(255) DEFAULT NULL,
		  `commercial_account_name` varchar(255) DEFAULT NULL,
		  `sales_rep_email` varchar(255) DEFAULT NULL,
		  `sales_rep_phone` varchar(255) DEFAULT NULL,
		  `order_comments` text DEFAULT NULL,
		  `invoicing_email` varchar(255) NOT NULL,
		  `number_of_pieces` int(12) DEFAULT NULL,
		  `estimated_order_total` decimal(12,2) DEFAULT NULL,
		  `order_type` enum('Standard','Complex','Program G') DEFAULT NULL,
		  `tax_title` VARCHAR(64) NULL, 
		  `tax_percent` DECIMAL(12,4) NULL,
		  `protect_code` varchar(64) NOT NULL,
		  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `order_params` TEXT NULL DEFAULT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE {$this->getTable('edform_orders')}
		  ADD PRIMARY KEY (`order_id`),
		  ADD KEY `user_id` (`user_id`);

		ALTER TABLE {$this->getTable('edform_orders')}
		  MODIFY `order_id` bigint(11) NOT NULL AUTO_INCREMENT;

		ALTER TABLE {$this->getTable('edform_orders')}
		  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `edform_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
		  
		  
		  
		
		CREATE TABLE {$this->getTable('edform_addresses')} (
		  `address_id` bigint(11) NOT NULL,
		  `firstname` varchar(255) DEFAULT NULL,
		  `lastname` varchar(255) DEFAULT NULL,
		  `street1` varchar(255) DEFAULT NULL,
		  `street2` varchar(255) DEFAULT NULL,
		  `city` varchar(255) DEFAULT NULL,
		  `state` varchar(255) DEFAULT NULL,
		  `region_id` INT(11) DEFAULT NULL,
		  `zip` varchar(10) DEFAULT NULL,
		  `phone` varchar(255) DEFAULT NULL,
		  `country_id` varchar(4) DEFAULT NULL,
		  `user_id` int(11) UNSIGNED DEFAULT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;


		ALTER TABLE {$this->getTable('edform_addresses')}
		  ADD PRIMARY KEY (`address_id`),
		  ADD KEY `user_id` (`user_id`);

		ALTER TABLE {$this->getTable('edform_addresses')}
		  MODIFY `address_id` bigint(11) NOT NULL AUTO_INCREMENT;

		ALTER TABLE {$this->getTable('edform_addresses')}
		  ADD CONSTRAINT `edform_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES {$this->getTable('edform_users')} (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
		COMMIT;  
		
		
		
		
		CREATE TABLE {$this->getTable('edform_progress')} (
		  `progress_id` bigint(11) NOT NULL,
		  `user_id` int(11) UNSIGNED NOT NULL,
		  `progress_data` text NOT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		
		ALTER TABLE {$this->getTable('edform_progress')}
		  ADD PRIMARY KEY (`progress_id`);
		
		
		ALTER TABLE {$this->getTable('edform_progress')}
		  MODIFY `progress_id` bigint(11) NOT NULL AUTO_INCREMENT;
		COMMIT;
		
		ALTER TABLE {$this->getTable('edform_progress')} ADD FOREIGN KEY (`user_id`) REFERENCES {$this->getTable('edform_users')}(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

		ALTER TABLE {$this->getTable('edform_progress')} ADD `client_name` VARCHAR(64) NULL DEFAULT NULL AFTER `user_id`, ADD `browser_id` VARCHAR(64) NULL DEFAULT NULL AFTER `client_name`, ADD UNIQUE `browser_id` (`browser_id`);

		ALTER TABLE {$this->getTable('edform_progress')} ADD `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;
				
		

		CREATE TABLE {$this->getTable('edform_editing')} (
		  `edit_id` bigint(11) NOT NULL,
		  `user_id` int(11) UNSIGNED NOT NULL,
		  `browser_id` VARCHAR(64) NULL DEFAULT NULL,
		  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;


		ALTER TABLE {$this->getTable('edform_editing')}
			ADD PRIMARY KEY (`edit_id`);
			
		ALTER TABLE {$this->getTable('edform_editing')}
		  MODIFY `edit_id` bigint(11) NOT NULL AUTO_INCREMENT;
		COMMIT;	

	");
	
	$this->endSetup();

?>