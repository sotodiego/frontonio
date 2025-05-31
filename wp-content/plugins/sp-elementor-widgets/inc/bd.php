<?php

	/*
		Espacio para la creacion de base de datos
		Hay que definir la version y mediante a ella realizar las actualizaciones

		//EJEMPLO =>
		
			$agencia_db = get_option("agencia_db", 0);

		<= EJEMPLO //

		Para la crecaion de la base de datos se puede usra la funcion "$this->table"
		
		//EJEMPLO =>
			
			$this->table(
	    		'test',
	    		[
	    			"`id` bigint(20) AUTO_INCREMENT NOT NULL",
	    			"`type` int(10) NOT NULL DEFAULT '0'",
	    			"`fecha` date NOT NULL",
	    			"`hora` int(10) NOT NULL DEFAULT '0'",
	    			"`estado` int(10) NOT NULL DEFAULT '0'",
	    			"`usuario` text(0) NOT NULL DEFAULT ''",
	    			"`create` timestamp(0) DEFAULT current_timestamp()",
			    	"`update` timestamp(0) DEFAULT current_timestamp() ON UPDATE current_timestamp()",
	    			"PRIMARY KEY (`id`)"
	    		]
	    	);

		<= EJEMPLO //

		Encapsilat todas las crecaiones en versiones y actualizar la ultima version

		//EJEMPLO =>
			if($agencia_db < 1){
				$this->table( *** );
				$this->table( *** );
				$this->table( *** );
				update_option("agencia_db", 1);
			}

		<= EJEMPLO //


	*/
	