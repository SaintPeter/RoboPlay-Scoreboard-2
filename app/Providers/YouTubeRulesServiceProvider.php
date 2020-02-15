<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class YouTubeRulesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
	    Validator::extend('yt_valid', function($attribute, $value, $parameters)
	    {
		    $value = str_ireplace('https', 'http', $value);
		    return preg_match("#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})|(^[A-Za-z0-9_-]{5,11})#", $value);
	    });

	    Validator::extend('yt_embeddable', function($attribute, $value, $parameters)
	    {
		    $value = str_ireplace('https', 'http', $value);
		    if(preg_match("#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})|(^[A-Za-z0-9_-]{5,11})#", $value, $matches)) {
			    return $this->yt_check(empty($matches[2]) ? $matches[3] : $matches[2], 'embeddable');
		    }
		    return false;
	    });

	    Validator::extend('yt_public', function($attribute, $value, $parameters)
	    {
		    $value = str_ireplace('https', 'http', $value);
		    if(preg_match("#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})|(^[A-Za-z0-9_-]{5,11})#", $value, $matches)) {
			    return $this->yt_check(empty($matches[2]) ? $matches[3] : $matches[2], 'privacyStatus');
		    }
		    return false;
	    });

	    Validator::extend('yt_length', function($attribute, $value, $parameters)
	    {
		    $value = str_ireplace('https', 'http', $value);
		    if(preg_match("#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})|(^[A-Za-z0-9_-]{5,11})#", $value, $matches)) {
			    $duration = $this->yt_check(empty($matches[2]) ? $matches[3] : $matches[2], '', 'duration');
			    $interval = new \DateInterval($duration);
			    $ref = new \DateTimeImmutable;
			    $endtime = $ref->add($interval);
			    $length = $endtime->getTimestamp() - $ref->getTimestamp();
			    return $length >= $parameters[0] && $length <= $parameters[1];
		    }
		    return false;
	    });

	    // Replace :min and :max parameters in the yt_length error message
	    Validator::replacer('yt_length', function($message, $attribute, $rule, $parameters) {
	    	$min = ltrim(gmdate("i:s", $parameters[0]),0);
		    $max = ltrim(gmdate("i:s", $parameters[1]),0);
	    	return str_replace(':min',$min,str_replace(':max', $max, $message));
	    });
    }

	function yt_check($code, $option, $contentDetail = "") {
		static $data;

		if(!isset($data)) {
			try {
				$url = "https://www.googleapis.com/youtube/v3/videos?part=status,contentDetails&id=" . $code . "&alt=json&key=" . config('services.youtube.key');
				$result = $this->curlhelper($url);
			} catch (\Exception $e) {
				return false;
			}
			$data = json_decode($result);
			//dd($code, $data);
		}
		if(property_exists($data, 'error')) {
			return false;
		}
		if(count($data->items) > 0) {
			if($contentDetail) {
				return $data->items[0]->contentDetails->$contentDetail;
			}
			return $data->items[0]->status->$option;
		}
		return false;
	}

	function curlhelper($url) {
		$curl = curl_init();
		if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
			curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER ,true);
		$response = curl_exec($curl);
		if(curl_errno($curl)) {
			throw new \Exception('CURL Error: ' . curl_error($curl));
		}
		return $response;
	}

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
