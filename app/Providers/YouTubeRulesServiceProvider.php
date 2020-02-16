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
	    Validator::extend('yt_valid', function($attribute, $value, $parameters) {
		    $value = str_ireplace('https', 'http', $value);
		    return preg_match("#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})|(^[A-Za-z0-9_-]{5,11})#", $value);
	    });

	    Validator::extend('yt_embeddable', function($attribute, $value, $parameters) {
	        return $this->yt_check($value, 'status.embeddable');
	    });

	    Validator::extend('yt_public', function($attribute, $value, $parameters) {
		    return $this->yt_check($value, 'status.privacyStatus') == 'public';
	    });

	    Validator::extend('yt_length', function($attribute, $value, $parameters) {
		    $duration = $this->yt_check($value, 'contentDetails.duration');
		    if($duration) {
			    $interval = new \DateInterval($duration);
			    $ref = new \DateTimeImmutable;
			    $end_time = $ref->add($interval);
			    $length = $end_time->getTimestamp() - $ref->getTimestamp();
			    return $length >= $parameters[0] && $length <= $parameters[1];
		    }
		    return false;
	    });

	    Validator::extend('yt_title_regex', function($attribute, $value, $parameters) {
		    $title = $this->yt_check($value, 'snippet.title');
		    if($title) {
			    return preg_match($parameters[0], $title);
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

	function yt_check($video_url, $option) {
		static $data;

		// Extract the YouTube Code
		$video_url = str_ireplace('https', 'http', $video_url);
		if(preg_match("#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})|(^[A-Za-z0-9_-]{5,11})#", $video_url, $matches)) {
			$code = empty($matches[2]) ? $matches[3] : $matches[2];
		} else {
			return false;
		}

		// Cache the API result
		if(!isset($data)) {
			try {
				$url = "https://www.googleapis.com/youtube/v3/videos?part=status,contentDetails,snippet&id=" . $code . "&alt=json&key=" . config('services.youtube.key');
				$result = $this->curlhelper($url);
			} catch (\Exception $e) {
				return false;
			}
			$data = json_decode($result);
		}
		// If error is set, everything is wrong and bad
		if(property_exists($data, 'error')) {
			return false;
		}

		// Ensure that the items and elements exist before returning
		if(count($data->items) > 0) {
			list($region, $element) = explode(".", $option);
			if(property_exists($data->items[0], $region)) {
				if(property_exists($data->items[0]->$region, $element)) {
					return $data->items[0]->$region->$element;
				}
			}
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
