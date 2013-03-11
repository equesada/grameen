<?php
class Page extends Controller {
   
   function Page()
   {
      parent::Controller();
   }
   
   function index()
   {
      
	  $a = 1;
	  //$this->template->set_master_template(base_url().'themes/theme1/template.php');
	  /*
	  $theme2['template'] = 'themes/theme1/template.php';
	  $theme2['regions'] = array(
		'header',
		'title',
		'content',
		'sidebar',
		'footer'
	  );
	  */
	  //$this->template->add_template('theme2', $theme2, TRUE);
	  //$this->template->set_template('theme2');
	  
	  // Write to $title
      $this->template->write('title', 'Welcome to the Template Library Docs!');
      
      // Write to $content
      $this->template->write_view('content', 'welcome_message');
      
      // Write to $sidebar
      $this->template->write_view('sidebar', 'sidebar3', $a, FALSE, 'sidebar2');
      
      // Render the template
      $this->template->render();
   }
   
}
?>