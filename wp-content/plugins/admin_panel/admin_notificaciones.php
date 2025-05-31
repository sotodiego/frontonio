<?php
	require_once ADPNSY_PATH . '/vendor/autoload.php';
	use Minishlink\WebPush\VAPID;
	use Minishlink\WebPush\WebPush;
	use Minishlink\WebPush\Subscription;

	class adpnsy_notify{

		private function auth(){
			if(file_exists(ADPNSY_PATH.'/json/keys.json')){
				return json_decode(file_get_contents(ADPNSY_PATH.'/json/keys.json'), true);
			}else{
				return self::create_auth();
			}
		}

		private function create_auth(){
			$keyset = VAPID::createVapidKeys();
			$keyset["subject"] = get_site_url();
			file_put_contents(ADPNSY_PATH.'/json/keys.json', json_encode($keyset));
			return $keyset;
		}

		public function register_not($user, $susc, $mensaje){
			global $wpdb;
			$admin_push_not = $wpdb->prefix . "admin_push_not";
			if( $wpdb->insert($admin_push_not, ["user" => $user, "suscripcion" => $susc, "mensaje" => $mensaje]) !== false){
				return true;
			}else{
				return false;
			}
		}

		public function get_auth(){
			return self::auth();
		}

		public function send_not($susc, $mensaje){

			if(strlen($susc) > 20){
			
				$subscription = Subscription::create(json_decode($susc, true));		

				$auth = array(
				    'VAPID' => self::auth()
				);

				$webPush = new WebPush($auth);

				$report = $webPush->sendOneNotification(
				    $subscription,
				    json_encode($mensaje)
				);

				if ($report->isSuccess()) {
				    return ["r" => true, "m" => "[v] Mensaje enviado"];
				} else {
				    return ["r" => false, "m" => "[x] Error de envio: {$report->getReason()}"];
				}
			}else{
				return ["r" => true, "m" => "[x] No activas"];
			}
		}

	}
	
?>