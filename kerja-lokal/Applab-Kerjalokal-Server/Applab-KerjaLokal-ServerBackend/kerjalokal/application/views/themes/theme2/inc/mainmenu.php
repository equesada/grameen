<table style="border : 1px solid #e0e0e0;" width="950" height="637" border="0" align="center">
  <tr>
    <td style="border : 1px solid #e0e0e0;" width="50%" valign="top">
    	<div id="container">

        <div id="header">
            <h1>Highlights</h1>
        </div>
    
        <div id="content">
        
            <div id="slider">
                <ul>				
                    <li><a href="#"><img src="<?php echo base_url() . "themes/theme2/images/01.jpg"?>" alt="Css Template Preview" /></a></li>
                    <li><a href="#"><img src="<?php echo base_url() . "themes/theme2/images/02.jpg"?>" alt="Css Template Preview" /></a></li>
                    <li><a href="#"><img src="<?php echo base_url() . "themes/theme2/images/03.jpg"?>" /></a></li>
                    <li><a href="#"><img src="<?php echo base_url() . "themes/theme2/images/04.jpg"?>" alt="Css Template Preview" /></a></li>
                    <li><a href="#"><img src="<?php echo base_url() . "themes/theme2/images/05.jpg"?>" alt="Css Template Preview" /></a></li>			
                </ul>
            </div>
        </div>
        </div>
    </td>
    <td style="border : 1px solid #e0e0e0;" width="25%" valign="top">
    	How It Works<br />
        <p></p>
        <p></p>
        <p></p>
        <p></p>
        <p></p>
       
        Info Kerja Works<br />
        <a href="#">Job Seeker</a><br />
        <a href="#">Job Mentor</a><br />
        <a href="#">Employer</a><br />
        </td>
    <td style="border : 1px solid #e0e0e0;" width="25%" valign="top">
    	20 - 26 March, 2011<br />
        <b>HR & Job seminar, JCC</b><br /><br />
        
        02 April, 2011<br />
        <b>Info Kerja Esia Lunch</b><br /><br />
        
        10 - 17 June, 2011<br />
        <b>West Java Vocational School Competition</b>
        
        
    </td>
  </tr>
  <tr>
    <td style="border : 1px solid #e0e0e0;">
    <div id='posts'>
	  	<?php foreach ($posts as $bt): ?>
    	<div>
        	<div><b><?php echo $bt->nama ?></b></div>
        	<span><?php echo $bt->email ?></span>
        	<p><?php echo $bt->pesan ?></p>
   		</div>
    	<hr />
		<?php endforeach; ?>
        <?php echo $this->pagination->create_links(); ?>
     </div>
    </td>
    <td style="border : 1px solid #e0e0e0;" valign="top">
    <p>Why Info kerja</p>
    
    <table style="border : 1px solid #e0e0e0;" width="220" height="260">
      <tr>
        <td valign="top">
        	Highlight Employer benefit:<br />
            Hire Faster<br />
            Pre Screening<br />
            Search Our Database<br />
            Instant Feedback<br />
            3 Months Free<br />
            <form id="form1" name="form1" method="post" action="">
              <p>
                <label>
                  <input type="submit" name="button" id="button" value="Download Brochure" />
                </label>
              </p>
              <p>
                <label>
                  <input type="submit" name="button2" id="button2" value="Take a Tour" />
                </label>
              </p>
              <p>
                <label>
                  <input type="submit" name="button3" id="button3" value="Registering is Free" />
                </label>
              </p>
            </form></td>
      </tr>
    </table></td>
    <td style="border : 1px solid #e0e0e0;" valign="top" align="center"><table style="border : 1px solid #e0e0e0;" width="80%" height="156">
      <tr>
        <td style="border : 1px solid #e0e0e0;" align="center">
        	Banner 1<br />
            See What's Hot on<br />
            Support Forum
        </td>
      </tr>
      <tr>
        <td style="border : 1px solid #e0e0e0;" align="center">
        	Banner 2<br />
            Article or Resources of the Week (or HR Corner)
        </td>
      </tr>
      <tr>
        <td style="border : 1px solid #e0e0e0;" align="center">
        	Banner 3<br />
            Client/Past Employer Testimonial
        </td>
      </tr>
    </table>
    <br />
    <p align="left"> 
    Follow Us
    </p>
    <p align="left">
    Subcribe to our Newsletter<br />
    
    </p>
    </td>
  </tr>
</table>
