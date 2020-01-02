<?php 

// Código para buscar thumbnail do vídeos do youtube e vímeo
if(!isset($_GET['video']) || empty($_GET['video'])){
	die('A URL do vídeo deve ser passado como parâmetro');
}
$video = $_GET['video'];
$thumbnail = getImgThumbURL($video);
echo '<img src="'. $thumbnail .'">';
echo '</br></br>';
echo $thumbnail;
saveImg($thumbnail);

// Define se o video pertence ao Youtube ou ao vímeo e retorana a URL da thumbnail
function getImgThumbURL($video = null){
	if($video){
		// verifica a URL para encontrar vídeo do Youtube ou Vimeo
		$pos = strpos(strtolower($video), 'youtu');
		if($pos){
			return getYouTubeThumbnail($video, 2);
		}else{
			// verifica a URL para encontrar vídeo do Vimeo
			$pos = strpos(strtolower($video), 'vimeo');
			if($pos){
				return getVimeoThumbnail($video, 2);
			}
			die('vídeo precisa não contem a URL do vídemo nem do Youtube');
		}
	}else{
		die('Url do vídeo precisa ser fornecida');
	}
}

// Recupera o ID do vídeo do Youtube e a thumbnail do vídeo e o tamanho da img seguindo os tamanhos disponiveis no youtube
function getYouTubeThumbnail($link, $size = 1) {
	$video_id = explode("?v=", $link);
	if (!isset($video_id[1])) {
		$video_id = explode("youtu.be/", $link);
	}
	$youtubeID = $video_id[1];
	if (empty($video_id[1])) $video_id = explode("/v/", $link);
	$video_id = explode("&", $video_id[1]);
	$youtubeVideoID = $video_id[0];
	if ($youtubeVideoID) {
		// seleciona o tamanho da igm passada no parâmetro na função
		switch ($size) {
			case 1:
			$size = '/sddefault.jpg';
			break;
			case 2: 
			$size = '/mqdefault.jpg';
			break;
			case 3:
			$size = '/hqdefault.jpg';
			break;
			case 4:
			$size = '/maxresdefault.jpg';
			break;
			default:
			$size = '/mqdefault.jpg';
			break;
		}
		$thumbURL = 'https://img.youtube.com/vi/' . $youtubeID . $size;
		return $thumbURL;
	} else {
		die('não foi possivel recuperar o ID do vídeo do Youtube');
	}
}

// Recupera o ID do vídeo do Vimeo e a thumbnail do vídeo escolhendo o tamanho da img seguindo os tamanhos disponiveis na vimeo 
function getVimeoThumbnail($link, $size = 2){
	$id = explode('vimeo.com/', $link);
	if(isset($id[1]) && !empty($id[1])){
		$id = $id[1];
	}else{
		die('Url do vídeo incorreta');
	}
	$url = "http://vimeo.com/api/v2/video/" . $id . ".json";
	$thumb = file_get_contents($url);
	$data = json_decode($thumb);
	// seleciona o tamanho da igm passada no parâmetro na função
	switch ($size) {
		case 1:
		$size = 'thumbnail_small';
		break;
		case 2: 
		$size = 'thumbnail_medium';
		break;
		case 3:
		$size = 'thumbnail_large';
		break;
		default:
		$size = 'thumbnail_large';
		break;
	}
	return $data[0]->{$size};
}

// salva a img em uma pasta
function saveImg($thumbnail = null, $path = null, $nome = null){
	if(!$path){
		$base_path = $_SERVER['DOCUMENT_ROOT'];
		@system("chmod -R 755 $base_path");
		$base_path .= '/thumb_img';
		if(!is_dir("$base_path")){
			@mkdir("$base_path", 0775);
		}
		$path = $base_path;
	}
	if($thumbnail){
		$ch = curl_init($thumbnail);
		if(!$nome){
			$nome =  time() . date('d-m-Y-H-i-s') .'.jpg';
		}
		$fp = fopen($path . '/' . $nome, 'wb');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}
}

function pre($data = null, $exit = false){
	echo '<pre>';
	print_r($data);
	echo '</pre>';
	if($exit){
		exit;
	}
}