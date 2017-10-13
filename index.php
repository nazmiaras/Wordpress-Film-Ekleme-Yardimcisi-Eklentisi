<?php

/*

Plugin Name: Nazmi Aras Film Ekleme Yardımcısı

Plugin URI: http://www.nazmiaras.com

Description: Film eklerken yardımcı araç

Author: Nazmi Aras

Version: 1.0

Author URI: http://www.nazmiaras.com

*/


add_action("admin_menu","botMenu");

function botMenu()

{
	add_menu_page( "Ana Sayfa", "Ay Filmler", 10, "ayfilmler_yardimci", "ayfilmler_yardimci", NULL, "145" );
}
function ayfilmler_yardimci(){
		if(!$_POST){
		?>
		<form action="admin.php?page=ayfilmler_yardimci" method="post">
		<h1>Film Ekle</h1>
		<label for="sinemelar_id">Film ID</label>
		<input type="number" class="form-control" id="film_id" name="film_id" placeholder="Örnek : 242098"></br>
		
		<label>Dil Seçeneği</label><br/>
		Türkçe Dublaj<input type="radio" class="form-control" id="turkce_dublaj" name="turkce_dublaj"></br>
		Türkçe Altyazı<input type="radio" class="form-control" id="turkce_altyazi" name="turkce_altyazi"></br>
		Yerli Yapım<input type="radio" class="form-control" id="yerli_yapim" name="yerli_yapim"></br>
		Orjinal Film<input type="radio" class="form-control" id="orjinal_film" name="orjinal_film"></br>
		Fragman<input type="radio" class="form-control" id="fragman" name="fragman"></br>
		
		<label>Kalite</label><br/>
		1080p<input type="radio" class="form-control" id="1080p" name="1080p"></br>
		720p<input type="radio" class="form-control" id="720p" name="720p"></br>
		480p<input type="radio" class="form-control" id="480p" name="480p"></br>
		360p<input type="radio" class="form-control" id="360p" name="360p"></br>
		240p<input type="radio" class="form-control" id="240p" name="240p"></br>
		
		<label for="link_1">Part 1</label>
		<input type="text" class="form-control" id="link_1" name="link_1" placeholder="Part 1 Embedi Buraya"></br>
		
		<label for="link_2">Part 2</label>
		<input type="text" class="form-control" id="link_2" name="link_2" placeholder="Part 2 Embedi Buraya"></br>
		
		<label for="link_2">Part 3</label>
		<input type="text" class="form-control" id="link_3" name="link_3" placeholder="Part 3 Embedi Buraya"></br>
		
		<label for="link_2">Fragman</label>
		<input type="text" class="form-control" id="fragman" name="fragman" placeholder="Fragman Embedi Buraya"></br>

		<input type="submit" name="yolla" id="yolla" value="Yolla" class="btn btn-default">
	</form>
<?php
}else{
// Tur
 
if($_POST['turkce_dublaj']){
	$dublaj = "Turkce Dublaj";
}
if($_POST['turkce_altyazi']){
	$dublaj = "Turkce Altyazili";
}
if($_POST['yerli_yapim']){
	$dublaj = "Yerli Film";
}
if($_POST['orjinal_film']){
	$dublaj = "Orjinal Film";
}
if($_POST['fragman']){
	$dublaj = "Fragman";
}
// Kalite
if($_POST['240p']){
	$kalite = "240p";
}
if($_POST['360p']){
	$kalite = "360p";
}
if($_POST['480p']){
	$kalite = "480p";
}
if($_POST['720p']){
	$kalite = "720p";
}
if($_POST['1080p']){
	$kalite = "1080p";
}
$siteadresi = "buraya site adresinizi giriniz";
$apikey = "Buraya TheMovieDb Api Anahtarınızı Giriniz";
$moviedb = file_get_contents("https://api.themoviedb.org/3/movie/".$_POST['film_id']."?api_key=".$apikey."&language=tr");
$json_moviedb = json_decode($moviedb);

$film_adi = $json_moviedb->title;
$imdb_id = $json_moviedb->imdb_id;
$alterisim = $json_moviedb->original_title;

$imdbapi = file_get_contents("http://www.omdbapi.com/?i=".$imdb_id."&plot=short&r=json");
$json_imdbapi = json_decode($imdbapi);
$imdb_puan = $json_imdbapi->imdbRating;
$yil = $json_imdbapi->Year;
$tarih = $json_imdbapi->release_date;
$yonetmen = $json_imdbapi->Director;
$oyuncular = $json_imdbapi->Actors;
$ozett = $json_moviedb->overview;
$image_url = "https://image.tmdb.org/t/p/w1920".$json_moviedb->poster_path;
$kategori = $json_moviedb->genres;

		INCLUDE_ONCE('../wp-load.php');
		INCLUDE_ONCE('../wp-config.php');
		$my_post = array();
		$my_post['post_title'] = $film_adi;
		$my_post['post_status'] = 'pending';
		$my_post['post_author'] = 1;
		$my_post['post_content'] = $ozett;
		//Kategori Bitiş
		// Yazıyı veritabanına ekle		
		if($post_id = wp_insert_post( $my_post )){
			echo "<h1>Tebrikler Başarılı Bir Şekilde Filmi Eklediniz</h1>";
		}else{
			echo "<h1>Üzgünüm Bir Şeyler Ters Gidiyor.</h1>";
		}
		
		// Taxonomyleri Ekliyelim
		wp_set_object_terms( $post_id, $yonetmen, 'yonetmen' );
		wp_set_object_terms( $post_id, $oyuncular, 'oyuncular' );
		wp_set_object_terms( $post_id, $imdb_puan, 'imdb' );
		wp_set_object_terms( $post_id, $yil, 'yapim' );
		// Etiketleyeim
		add_post_meta($post_id,"_aioseop_title",$film_adi." Full HD İzle 1080P İzle Sansürsüz İzle");
		add_post_meta($post_id,"_aioseop_description",$film_adi." Full HD İzle 1080P İzle Sansürsüz İzle , En Güncel Filmler Ve Daha Fazlası Burada");
		add_post_meta($post_id,"_aioseop_keywords",str_replace(" ",",",$film_adi." Full HD İzle 1080P İzle Sansürsüz İzle"));
		// Film Bilgileri Etiketleri
		add_post_meta($post_id,"imdb",$imdb_puan);
		add_post_meta($post_id,"yapim",$tarih);
		add_post_meta($post_id,"yonetmen",$yonetmen);
		add_post_meta($post_id,"oyuncular",$oyuncular);
		add_post_meta($post_id,"alterisim",$alterisim);
		add_post_meta($post_id,"sure",$sure);
		add_post_meta($post_id,"dublaj",$dublaj);
		add_post_meta($post_id,"kalite",$kalite);
		if($_POST['fragman']){add_post_meta($post_id,"fragman",$_POST['fragman']);}
		if(!$_POST['link_3']){
		if(!$_POST['link_2']){add_post_meta($post_id,"tekpart",$_POST['link_1']);}else{
			add_post_meta($post_id,"tekpart",$_POST['link_1']);
			add_post_meta($post_id,"videozer",$_POST['link_2']);
		}}else{
			add_post_meta($post_id,"tekpart",$_POST['link_1']);
			add_post_meta($post_id,"videozer",$_POST['link_2']);
			add_post_meta($post_id,"embed1",$_POST['link_3']);
		}?>
		<input onclick="location.href='<?=$siteadresi;>/wp-admin/post.php?post=<?php echo $post_id;?>&action=edit'" type="button" name="filmi_onayla" id="filmi_onayla" value="Yazılara Git" />
		<input onclick="location.href='<?=$siteadresi;>/wp-admin/admin.php?page=ayfilmler_yardimci'" type="button" name="yeni_film_ekle" id="yeni_film_ekle" value="Yeni Film Ekle" />
		<?php	
		/**
** Öne çıkarılmış görseli ekliyeceğiz
**/
$upload_dir = wp_upload_dir();
$image_data = file_get_contents($image_url);
$filename = basename($image_url);
if(wp_mkdir_p($upload_dir['path'])){
$file = $upload_dir['path'] . '/' . $filename;}
else
$file = $upload_dir['basedir'] . '/' . $filename;
file_put_contents($file, $image_data);
$wp_filetype = wp_check_filetype($filename, null );
$attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
);
$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
require_once(ABSPATH . 'wp-admin/includes/image.php');
$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
wp_update_attachment_metadata( $attach_id, $attach_data );
set_post_thumbnail( $post_id, $attach_id );}}?>