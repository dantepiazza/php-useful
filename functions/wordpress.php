<?php	
	
	function wp_async_scripts($url){
		if(strpos($url, '#asyncload') === false){
			return $url;
		}
		else if(is_admin()){
			return str_replace('#asyncload', '', $url);
		}
		else{
			return str_replace('#asyncload', '', $url)."' async='async"; 
		}
    }
	
	function get_post_header($type = 'full'){	
		$image = get_bloginfo('template_url').'/imagenes/articulos-thumbnail-'.$type.'.jpg';
		
		if(has_post_thumbnail()){
			if($image = get_the_post_thumbnail_url(null, $type)){
				$retorno = 'style="background-image:url('.$image.');"';
			}
		}

		if(!empty($retorno)){
			return $retorno;
		}

		return '';
	}
	
	function get_post_background($type = 'full'){	
		return get_post_header($type);
	}	

	function the_post_background(){	
		echo get_post_background();
	}
	
	function the_breadcrumb($id = null, $options = null){
		if(!is_null($id)){
			global $post;
			
			$id = get_post($id);
		
			setup_postdata($id);
		}
		
		$defaults = array(
			'home_text' => 'Portada',
			'separator' => ' &raquo; '
		);	
		
		if(!is_null($options)){
			$options = array_merge($defaults, $options);
		}
		else{
			$options = $defaults;
		}		
		
		$portada = '<a href="'.get_option('home').'">'.$options['home_text'].'</a>';
	
		if(!is_home()){
			if(is_category() or is_single()){
				echo($portada);
				
				if(!is_singular(array('post', 'page') )){
					echo($options['separator'].'<a href="'.get_post_type_archive_link(get_post_type()).'" '.((is_post_type_archive(get_post_type())) ? 'class="current-breadcrumb-item"' : '').'>'.get_post_type_object(get_post_type()) -> label.'</a>');
				
					the_taxonomies(array('post' => 0, 'before' => $options['separator'], 'sep' => $options['separator'], 'after' => '', 'template' => '%s: %l.')); 
				}
				else{				
					the_category($options['separator']);
				}				

				if(is_single()){
					echo($options['separator'].'<a href="'.get_the_permalink().'" class="current-breadcrumb-item">'.get_the_title().'</a>');
				}
			} 
			else if(is_page()){			
				$superior = get_post_ancestors(get_the_ID());
			
				if($superior){
					$superior = array_reverse($superior);
					$redistibucion = array();
					
					foreach($superior as $pagina){
						$redistibucion[] = '<a href="'.get_the_permalink($pagina).'">'.get_the_title($pagina).'</a>';
					}
					
					echo($portada.$options['separator'].implode($options['separator'], $redistibucion).$options['separator'].'<a href="'.get_the_permalink().'" class="current-breadcrumb-item">'.get_the_title().'</a>');
				}
				else{
					echo($portada.$options['separator'].'<a href="'.get_the_permalink().'" class="current-breadcrumb-item">'.get_the_title().'</a>');
				}
			}
			else{
				echo($portada.$options['separator'].'<a href="'.get_the_permalink().'" class="current-breadcrumb-item">'.get_the_title().'</a>');
			}
		}
		else{
			echo($portada.$options['separator']);
		}		
	} 
	
	function the_resume_nav(){
		get_the_resume_nav(true);
	}
	
	function get_the_resume_nav($mostrar = false){		
		$exploracion = function($_titulos, $_tag = 1, $_profundidad = 1) use ( &$exploracion ){
			$redistribucion = array();
					
			foreach($_titulos[0] as $clave => $valor){
				//Se trata de la posiscion de la etiqueta EJ: <h1>Contenido (h1 es la posicion 1 ya que < es la 0)

				if(strpos($valor, '<h'.$_tag) !== false){
					$redistribucion[$clave]['enlace'] = $_titulos[1][$clave];
					$redistribucion[$clave]['titulo'] = $_titulos[2][$clave];
										
					if(isset($_titulos[0][$clave + 1])){
						if(strpos($_titulos[0][$clave + 1], '<h'.($_tag + 1)) === 0){
							$redistribucion[$clave]['hijos'] = $exploracion($_titulos, $_tag + 1, $_profundidad);
						}
					}
				}
				
					
			}
								
			if(empty($redistribucion)){
				if($_profundidad < 6){
					$redistribucion = $exploracion($_titulos, $_tag + 1, $_profundidad + 1);
				}
			}
									
			return $redistribucion;
		};
		
		$muestreo = function($_elementos) use ( &$muestreo ){
			echo('<ul>');
			
			foreach($_elementos as $elemento){
				echo('<li>');
				echo('<a href="#'.$elemento['enlace'].'" >'.$elemento['titulo'].'</a>');
									
				if(isset($elemento['hijos'])){
					echo('<ul>');
					echo($muestreo($elemento['hijos']));
					echo('</ul>');
				}
				
				echo('</li>');
			}
			
			echo('</ul>');
		};

		#############################
				
		$string = get_the_content();
		
		preg_match_all('|<\s*h[1-6](?:.*) id=["\'](.*)["\']>(.*)</\s*h|Ui', $string, $titulos);
									
		$retorno = $exploracion($titulos);							
					
		if($mostrar){
			echo($muestreo($retorno));
		}
		else{
			return $retorno;
		}
	}
	
	function get_analytic_redirect($url, $identificador = null){
		if(!empty($identificador) and !is_null($identificador)){
			$cadena = 'i='.$identificador.'&';
		}
		
		return site_url('/redireccion/?'.$cadena.'r='.urlencode($url));
	}
		
	function the_title_bbcode($title, $id = null){
		$bbcode = array('[b]', '[/b]');
		
		if(is_admin() or current_filter() == 'wp_title'){
			$remplazo = '';
		}
		else{
			$remplazo = array('<strong>', '</strong>');
		}
		
		return str_replace($bbcode, $remplazo, $title);
	}
	
	function get_template_custom($template, $meta = null, $id = null){
		$retorno = '';
		
		$paginas = get_posts(array('post_type' => 'page', 'fields' => 'ids', 'nopaging' => true, 'meta_key' => '_wp_page_template', 'meta_value' => $template));
		
		foreach($paginas as $pagina){
			if(!is_null($meta) and !empty($meta)){
				$retorno = get_post_meta($pagina, $meta, true);
			}
			else{
				$metas[$pagina] = get_post_custom($pagina);
								
				foreach($metas as $page => $valores){
					foreach($valores as $clave => $valor){
						$redistribucion[$page][$clave] = $valor[0];
					}
				}
				
				$retorno = $redistribucion;
			}
		}
			
		if(is_null($meta) and !is_null($id) and !empty($id) and isset($retorno[$id])){
			return $retorno[$id];
		}
		
		return $retorno;
	}
	
	function the_short_excerpt($title_length = 50, $length = null){
		$title = get_the_title();
		$excerpt = get_the_excerpt();
		
		if(!is_array($length)){
			$length = array(170, 120);
		}
		
		if(strlen($title) > $title_length){
			echo(substr($excerpt, 0, strrpos(substr($excerpt, 0, $length[1]), ' ')));
		}
		else{
			if(strlen($excerpt) > 170){
				echo(substr($excerpt, 0, strrpos(substr($excerpt, 0, $length[0]), ' ')));
			}
			else{
				echo($excerpt);
			}
		}
	}
	
	function get_the_content_part($part = null){
		$content = get_the_content();
		
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		
		if(!is_null($part)){
			preg_match_all('/<!--(.*)-->(.*)<!--\/.*-->/sU', $content, $partes);
			
			for($i = 0; $i < count($partes[1]); $i++){
				$redistribucion[$partes[1][$i]] = $partes[2][$i];
			}
			
			if(isset($redistribucion[$part])){
				return $redistribucion[$part];
			}
		}
		else{
			$content = preg_replace('/<!--(.*)-->(.*)<!--\/.*-->/sU', '', $content);		
		}
		
		return $content;
	}
	
	function the_content_part($part){
		echo(get_the_content_part($part));
	}
	
	function the_first_category(){
		$category = get_the_category(); 
		
		if(isset($category[0])){
			echo '<a class="category" href="'.get_category_link($category[0] -> term_id ).'">'.$category[0] -> cat_name.'</a>'; 
		}
	}
	
	function get_the_first_category(){
		$category = get_the_category(); 
		
		echo $category[0] -> cat_name; 
	}
	
	function wp_list_category_custom($taxonomy){
		$categories = get_terms(array('taxonomy' => $taxonomy));
		
		if(! $categories || is_wp_error($categories)){
			$categories = array();
		}			
	 
		$categories = array_values($categories);
	 
		foreach(array_keys($categories) as $key){
			_make_cat_compat($categories[$key]);
		}
		
		if(!empty($categories)){
			echo('<ul>');
			
			foreach($categories as $category){
				echo('<li><a class="category" href="'.get_term_link($category -> term_id, $taxonomy).'">'.$category -> cat_name.'</a></li>');
			}
			
			echo('</ul>');
		}
	}
	
	function get_the_category_custom($taxonomy, $id = false){
		$categories = get_the_terms($id, $taxonomy);
		
		if(! $categories || is_wp_error($categories)){
			$categories = array();
		}			
	 
		$categories = array_values($categories);
	 
		foreach(array_keys($categories) as $key){
			_make_cat_compat($categories[$key]);
		}

		return apply_filters('get_the_categories', $categories, $id);
	}

	function wp_admin_notice($html, $tipo = 'exito'){
		if(!empty($html)){
			$tipos = array(
				'exito' => 'notice-success',
				'error' => 'notice-error',
				'alerta' => 'notice-warning'
			);
						
			echo('<div class="notice '.$tipos[$tipo].'"><p>'.$html.'</p></div>');
		}
	}
	
	function wp_mail_filter($variables){
		$index = isset($variables['html']) ? 'html' : 'message';

		$variables[$index] = preg_replace("/\<((https|http)(.*))\>/", "$2$3", $variables[$index]);

		if($index === 'message'){
			//$variables['message'] = nl2br($variables['message']);
		}
		
		add_filter('wp_mail_content_type', function($content_type){ return 'text/html'; });
		
		$plantilla = null;

		if(is_array($variables['headers'])){
			$plantilla = $variables['headers'][1];
			$variables['headers'] = $variables['headers'][0];
		}
		
		if(strpos($variables['headers'], '<' ) !== false){
			$emisor = substr( $variables['headers'], strpos( $variables['headers'], '<' ) + 1 );
			$emisor = str_replace( '>', '', $emisor );
			$emisor = trim($emisor);
		} 
		else{
			$emisor = trim($variables['headers']);
		}
		
		$etiquetas['asunto'] = $variables['subject'];
		$etiquetas['mensaje'] = $variables['message'];
		$etiquetas['emisor'] = $emisor;
		$etiquetas['destinatario'] = trim($variables['to']);

		if(file_exists(get_template_directory().'/plantilla-mail.php')){
			$plantilla = file_get_contents(get_template_directory().'/plantilla-mail.php');
		}
		else{
			$plantilla = '';
		}
					
		$wordpress_tags = array(
			'admin_email', 
			'atom_url', 
			'charset', 
			'comments_atom_url', 
			'comments_rss2_url', 
			'description', 
			'home',
			'html_type', 
			'language', 
			'name', 
			'pingback_url', 
			'rdf_url', 
			'rss2_url', 
			'rss_url', 
			'siteurl', 
			'stylesheet_directory',
			'stylesheet_url', 
			'template_directory', 
			'template_url', 
			'text_direction', 
			'url', 
			'version', 
			'wpurl'
		);
			
		if(!empty($etiquetas) and is_array($etiquetas)){
			foreach($etiquetas as $etiqueta => $valor){
				$plantilla = str_replace('['.$etiqueta.']', $valor, $plantilla);
			}
				
			foreach($wordpress_tags as $tag){
				$plantilla = str_replace('['.$tag.']', @get_bloginfo($tag), $plantilla);
			}
		}	

		$variables['message'] = $plantilla;

		return $variables;
	}
	
?>