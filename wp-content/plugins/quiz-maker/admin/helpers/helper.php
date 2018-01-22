<?php
class AYS_Quiz_Helper{
	public static function ays_redirect($url){
		?>
			<script>
				window.location.href = "<?php echo $url;?>";
			</script>
		<?php
	}
}
?>

