<?php

final class PrintPDF {

	private $loc;
	private $mod;

	public function __construct($loc = array(), $mod = array()) {
		$this->loc = $loc;
		$this->mod = $mod;
	}

	public function filename($type = 'pdf') {

		$f = 'download.' . $type;

		if (!empty($this->loc)) {

			$loc = $this->loc;
			$loc = array_filter($loc);
			foreach ($loc AS $key => $value) {
				if ($value == 'pdf' || in_array($value,$this->mod)) { unset($loc[$key]); }
			}
			$f = join('-',$loc) . '_' . date('Ymd-his' ) . '.' . $type;

		}

		return $f;

	}

}

class PerihelionMPDF {

	protected $mpdf;

	public function __construct($doc = null) {

		$this->mpdf =  $this->createMpdfInstance(/*$orientation=*/'P');
		if (!is_null($doc)) {
			$this->output($doc);			
		}
	}

	public function getMpdf() {
		return $this->mpdf;
	}

	protected function createMpdfInstance($orientation) {

		$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
		$fontDirs = $defaultConfig['fontDir'];

		$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
		$fontData = $defaultFontConfig['fontdata'];

		$this->mpdf = new \Mpdf\Mpdf([
			'orientation' => $orientation,	// 'P':縦向き、'L':横向き
			'fontDir' => array_merge($fontDirs, [
				'perihelion/assets/fonts',
			]),
			'fontdata' => [
				'takao_pgothic' => ['R' => 'TakaoPGothic.ttf'],
				'takao_gothic' => ['R' => 'TakaoGothic.ttf'],
				'takao_pmincho' => ['R' => 'TakaoPMincho.ttf'],
				'takao_mincho' => ['R' => 'TakaoMincho.ttf']
			],
			'default_font' => 'takao_pgothic',
			'tempDir' => sys_get_temp_dir()
		]);
		$this->mpdf->autoScriptToLang = true;
		$this->mpdf->autoLangToFont = true;

		return $this->mpdf;
	}

	public function output($doc) {
		$stylesheet = file_get_contents(Config::read('web.root') . 'perihelion/assets/css/print.css');
		$this->mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
		$this->mpdf->WriteHTML($doc);
	}

}

?>