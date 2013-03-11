package org.applab.KerjaLokal;


import android.os.Bundle;

import org.apache.cordova.*;

public class KerjaLokalMain extends DroidGap {
    @Override
    public void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState); 
        super.setStringProperty("loadingDialog", "Job Market v2,Loading....");
//        super.setStringProperty("errorUrl", "file:///android_asset/www/error.html");
//        super.loadUrl("file:///android_asset/www/kerjalokal_main.html");
        //super.loadUrl("http://180.243.231.8:8085/api/request.php");
        super.loadUrl("http://ec2-107-20-14-148.compute-1.amazonaws.com/api/request.php");
        
    }
//public class HelloPhoneGapActivity extends Activity {
    /** Called when the activity is first created. */
//    @Override
//    public void onCreate(Bundle savedInstanceState) {
//        super.onCreate(savedInstanceState);
//        setContentView(R.layout.main);
//    }
}