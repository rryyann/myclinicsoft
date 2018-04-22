<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MX_Controller {

	public function index()
	{
		$this->layout
			->title('Welcome PRB') //$article->title
			// application/views/some_folder/header
			//->set_partial('header', 'includes/widgets') //third param optional $data
			// application/views/some_folder/header
			//->inject_partial('header', '<h1>Hello World!</h1>')  //third param optional $data
			->set_layout('one-column') // application/views/layouts/two_col.php
			->build('welcome'); // views/welcome_message
	}
}
	