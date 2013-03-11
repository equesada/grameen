package org.grameen.mobileSurvey.utilities;

import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.grameen.mobileSurvey.application.Collect;
import org.grameen.mobileSurvey.preferences.PreferencesActivity;

import android.content.Context;
import android.preference.PreferenceManager;
import android.telephony.TelephonyManager;

/**
 * This class has the responsability of managing the login in
 * the grameen foundation services
 */
public class GFLoginProvider {
	private static final String IMEI = "imei";
	private static final String PASS = "pass";
	private static final String USER = "user";

	private static String username;
	private static String password;
	private static String imei;

	//just to write less
	private static String getPreference(String key, String value){
		return PreferenceManager.getDefaultSharedPreferences(Collect.getInstance()).getString(key, value);
	}

	private static void getUsernamePasswordAndImei(){
		username = getPreference(PreferencesActivity.KEY_USERNAME, "");
		password = getPreference(PreferencesActivity.KEY_PASSWORD, "");
		//getting IMEI
		TelephonyManager telephonyManager = (TelephonyManager)Collect.getInstance().getSystemService(Context.TELEPHONY_SERVICE);
		imei =  telephonyManager.getDeviceId();
	}
	public static HttpGet addSalesforceLogin(HttpGet httpGet){
		if (getPreference(PreferencesActivity.KEY_PROTOCOL, null).equals(PreferencesActivity.PROTOCOL_GRAMEEN_SALESFORCE)){
			getUsernamePasswordAndImei();
			httpGet.addHeader(USER, username);
			httpGet.addHeader(PASS,password);
			httpGet.addHeader(IMEI,imei);
		}
		return httpGet;
	}

	public static HttpPost addSalesforceLogin(HttpPost httpPost){
		if (getPreference(PreferencesActivity.KEY_PROTOCOL, null).equals(PreferencesActivity.PROTOCOL_GRAMEEN_SALESFORCE)){
			getUsernamePasswordAndImei();
			httpPost.addHeader(USER, username);
			httpPost.addHeader(PASS,password);
			httpPost.addHeader(IMEI,imei);
		}
		return httpPost;
	}
}
