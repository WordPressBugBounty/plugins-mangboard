<?php
if(!function_exists('mbw_init_htmlpurifier')){
	function mbw_init_htmlpurifier(){
		if(mbw_get_trace("mbw_init_htmlpurifier")==""){
			mbw_add_trace("mbw_init_htmlpurifier");
			if(!class_exists('HTMLPurifier')){
				require_once(MBW_PLUGIN_PATH."plugins/htmlpurifier/HTMLPurifier.standalone.php");
			}

			if(!class_exists('HTMLPurifier_Filter_EscapeTextContent')){
				class HTMLPurifier_Filter_EscapeTextContent extends HTMLPurifier_Filter{
					/**
					 * Name of the filter for identification purposes.
					 * @type string
					 */
					public $name = 'EscapeTextContent';

					/**
					 * Post-processor function, handles HTML after HTML Purifier
					 * @param string $html
					 * @param HTMLPurifier_Config $config
					 * @param HTMLPurifier_Context $context
					 * @return string
					 */
					public function postFilter($html, $config, $context)
					{
						return preg_replace_callback('#(?<=^|>)[^><]+?(?=<|$)#', array($this, 'postFilterCallback'), $html);
					}

					protected function postFilterCallback($matches)
					{
						// @see https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet#RULE_.231_-_HTML_Escape_Before_Inserting_Untrusted_Data_into_HTML_Element_Content
						$content = html_entity_decode($matches[0]);
						return str_replace(
								array('&', '"', "'", '<', '>', '(', ')', '/'), 
								array('&amp;', '&quot;', '&#39;', '&lt;', '&gt;', '&#40;', '&#41;', '&#47;'), 
								$content
						);
					}
				}
			}
		}
	}
}

if(!function_exists('mbw_get_htmlpurify')){
	function mbw_get_htmlpurify($value){		
		mbw_init_htmlpurifier();
		if(is_array($value)) return array_map('mbw_get_htmlpurify', $value);
		$config			= HTMLPurifier_Config::createDefault();
		if(class_exists('HTMLPurifier_Filter_EscapeTextContent')) $config->set('Filter.Custom', array(new HTMLPurifier_Filter_EscapeTextContent) );
		$config->set('HTML.SafeObject', true);
		$config->set('HTML.SafeEmbed', true);
		$config->set('HTML.TidyLevel', 'light');
		$config->set('HTML.FlashAllowFullScreen', true);
		$config->set('Output.FlashCompat', true);
		$config->set('HTML.SafeIframe', true);
		//$config->set('HTML.Allowed', 'p[style],p,br,hr,h1,h2,h3,h4,h5,h6,center,em,u,ul,li,font,ol,div[class|style],span[style],table,thead,tbody,tfoot,tr,td,th,blockquote,strike,b,strong,img[src|alt|style|class|title|height|width],a[href|rel|target],iframe[src|width|height|frameborder]');
		//$config->set('HTML.AllowedAttributes','style,id,class,dir,title,lang,colspan,rowspan,a.href,a.target,img.src,img.style,img.width,img.height,img.alt,xml:lang');
		$config->set('Core.RemoveInvalidImg', true);
		$config->set('URI.AllowedSchemes', array('http' => true, 'https' => true, 'mailto' => true, 'tel' => true, 'data' => true));
		//$config->set('Cache.DefinitionImpl', null);		//캐시 사용안함				
		//https://youtu.be/oaEkgX7AQ_0?si=jFGj6yYj0B3VYOD3
		$config->set('URI.SafeIframeRegexp', '%^(?:https?:)?\/\/(?:'.implode('|', array(
			'www\.youtube(?:-nocookie)?\.com\/',
			'www\.youtube\.com\/',
			'youtube\.com\/',
			'maps\.google\.com\/',
			'docs\.google\.com\/',
			'calendar\.google\.com\/',
			'drive\.google\.com\/',
			'player\.vimeo\.com\/video\/',
			'www\.dailymotion\.com\/embed\/video\/',
			'tv\.kakao\.com\/embed\/player\/cliplink\/',
			'www\.microsoft\.com\/showcase\/video\.aspx',
			'(?:serviceapi\.nmv|player\.music)\.naver\.com\/',
			'(?:api\.v|flvs|tvpot|videofarm)\.daum\.net\/',
			'v\.nate\.com\/',
			'play\.mgoon\.com\/',
			'channel\.pandora\.tv\/',
			'play\.afreecatv\.com\/afmlb01\/',
			'www\.afreeca\.com\/player\/Player\.swf',
			'static\.afreecatv\.com\/',
			'player\.mnet\.com\/',
			'sbsplayer\.sbs\.co\.kr\/',
			'img\.lifestyler\.co\.kr\/',
			'www\.slideshare\.net\/',
			'm\.afreecatv\.com\/#\/player',
            'serviceapi\.rmcnmv\.naver\.com\/flash\/',
			'play-tv\.kakao\.com\/embed\/player\/cliplink\/',
			'www\.tving\.com\/ifm\/pick\/outPlayer\/S\/',
			'play\.afreecatv\.com\/',
			'live-stream-manager\.afreecatv\.com\/',
			'www\.twitch\.tv\/',
			'player\.twitch\.tv\/',
			'www\.twitch\.tv\/embed\/',
			'www\.twitch\.tv\/videos\/',
			'res\.afreecatv\.com\/',
			'clips\.twitch.tv\/embed\/'
		)).')%');

		$config->set('Attr.AllowedFrameTargets', array('_blank'));
		$config->set('HTML.MaxImgLength', null);
		$config->set('CSS.MaxImgLength', null);
		$config->set('Core.Encoding', mbw_get_option("encoding"));
		$config->set('Cache.SerializerPath', rtrim(MBW_UPLOAD_PATH,'/'));

		$def = $config->getHTMLDefinition(true);
		$def->addElement('video','Block','Flow','Common', array('src' => 'URI','width' => 'Length','height' => 'Length', 'type' => 'Text','poster' => 'URI','style' => 'Text','id' => 'Text','controls' =>'Bool','autoplay' =>'Bool','muted' =>'Bool','playsinline' => 'Bool','loop' => 'Bool'));
		$def->addAttribute('iframe', 'allowfullscreen', 'Bool');

		$purifier			= new HTMLPurifier($config);
		$html			= $purifier->purify($value);
		return $html;
	}
}
?>