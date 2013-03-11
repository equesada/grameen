global class PostInstallScript implements InstallHandler {

    global static final Integer MAJOR_VERSION = 1;
    global static final Integer MINOR_VERSION = 2;
    global static final Integer PATCH_VERSION = 0;

    global static final Map<String,String> allCountryCodes = new Map<string,string>{'AF' => 'AFGHANISTAN',
                                                        'AX' => 'ÅLAND ISLANDS',
                                                        'AL' => 'ALBANIA',
                                                        'DZ' => 'ALGERIA',
                                                        'AS' => 'AMERICAN SAMOA',
                                                        'AD' => 'ANDORRA',
                                                        'AO' => 'ANGOLA',
                                                        'AI' => 'ANGUILLA',
                                                        'AQ' => 'ANTARCTICA',
                                                        'AG' => 'ANTIGUA AND BARBUDA',
                                                        'AR' => 'ARGENTINA',
                                                        'AM' => 'ARMENIA',
                                                        'AW' => 'ARUBA',
                                                        'AU' => 'AUSTRALIA',
                                                        'AT' => 'AUSTRIA',
                                                        'AZ' => 'AZERBAIJAN',
                                                        'BS' => 'BAHAMAS',
                                                        'BH' => 'BAHRAIN',
                                                        'BD' => 'BANGLADESH',
                                                        'BB' => 'BARBADOS',
                                                        'BY' => 'BELARUS',
                                                        'BE' => 'BELGIUM',
                                                        'BZ' => 'BELIZE',
                                                        'BJ' => 'BENIN',
                                                        'BM' => 'BERMUDA',
                                                        'BT' => 'BHUTAN',
                                                        'BO' => 'BOLIVIA, PLURINATIONAL STATE OF',
                                                        'BQ' => 'BONAIRE, SINT EUSTATIUS AND SABA',
                                                        'BA' => 'BOSNIA AND HERZEGOVINA',
                                                        'BW' => 'BOTSWANA',
                                                        'BV' => 'BOUVET ISLAND',
                                                        'BR' => 'BRAZIL',
                                                        'IO' => 'BRITISH INDIAN OCEAN TERRITORY',
                                                        'BN' => 'BRUNEI DARUSSALAM',
                                                        'BG' => 'BULGARIA',
                                                        'BF' => 'BURKINA FASO',
                                                        'BI' => 'BURUNDI',
                                                        'KH' => 'CAMBODIA',
                                                        'CM' => 'CAMEROON',
                                                        'CA' => 'CANADA',
                                                        'CV' => 'CAPE VERDE',
                                                        'KY' => 'CAYMAN ISLANDS',
                                                        'CF' => 'CENTRAL AFRICAN REPUBLIC',
                                                        'TD' => 'CHAD',
                                                        'CL' => 'CHILE',
                                                        'CN' => 'CHINA',
                                                        'CX' => 'CHRISTMAS ISLAND',
                                                        'CC' => 'COCOS (KEELING) ISLANDS',
                                                        'CO' => 'COLOMBIA',
                                                        'KM' => 'COMOROS',
                                                        'CG' => 'CONGO',
                                                        'CD' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
                                                        'CK' => 'COOK ISLANDS',
                                                        'CR' => 'COSTA RICA',
                                                        'CI' => 'CÔTE D\'IVOIRE',
                                                        'HR' => 'CROATIA',
                                                        'CU' => 'CUBA',
                                                        'CW' => 'CURAÇAO',
                                                        'CY' => 'CYPRUS',
                                                        'CZ' => 'CZECH REPUBLIC',
                                                        'DK' => 'DENMARK',
                                                        'DJ' => 'DJIBOUTI',
                                                        'DM' => 'DOMINICA',
                                                        'DO' => 'DOMINICAN REPUBLIC',
                                                        'EC' => 'ECUADOR',
                                                        'EG' => 'EGYPT',
                                                        'SV' => 'EL SALVADOR',
                                                        'GQ' => 'EQUATORIAL GUINEA',
                                                        'ER' => 'ERITREA',
                                                        'EE' => 'ESTONIA',
                                                        'ET' => 'ETHIOPIA',
                                                        'FK' => 'FALKLAND ISLANDS (MALVINAS)',
                                                        'FO' => 'FAROE ISLANDS',
                                                        'FJ' => 'FIJI',
                                                        'FI' => 'FINLAND',
                                                        'FR' => 'FRANCE',
                                                        'GF' => 'FRENCH GUIANA',
                                                        'PF' => 'FRENCH POLYNESIA',
                                                        'TF' => 'FRENCH SOUTHERN TERRITORIES',
                                                        'GA' => 'GABON',
                                                        'GM' => 'GAMBIA',
                                                        'GE' => 'GEORGIA',
                                                        'DE' => 'GERMANY',
                                                        'GH' => 'GHANA',
                                                        'GI' => 'GIBRALTAR',
                                                        'GR' => 'GREECE',
                                                        'GL' => 'GREENLAND',
                                                        'GD' => 'GRENADA',
                                                        'GP' => 'GUADELOUPE',
                                                        'GU' => 'GUAM',
                                                        'GT' => 'GUATEMALA',
                                                        'GG' => 'GUERNSEY',
                                                        'GN' => 'GUINEA',
                                                        'GW' => 'GUINEA-BISSAU',
                                                        'GY' => 'GUYANA',
                                                        'HT' => 'HAITI',
                                                        'HM' => 'HEARD ISLAND AND MCDONALD ISLANDS',
                                                        'VA' => 'HOLY SEE (VATICAN CITY STATE)',
                                                        'HN' => 'HONDURAS',
                                                        'HK' => 'HONG KONG',
                                                        'HU' => 'HUNGARY',
                                                        'IS' => 'ICELAND',
                                                        'IN' => 'INDIA',
                                                        'ID' => 'INDONESIA',
                                                        'IR' => 'IRAN, ISLAMIC REPUBLIC OF',
                                                        'IQ' => 'IRAQ',
                                                        'IE' => 'IRELAND',
                                                        'IM' => 'ISLE OF MAN',
                                                        'IL' => 'ISRAEL',
                                                        'IT' => 'ITALY',
                                                        'JM' => 'JAMAICA',
                                                        'JP' => 'JAPAN',
                                                        'JE' => 'JERSEY',
                                                        'JO' => 'JORDAN',
                                                        'KZ' => 'KAZAKHSTAN',
                                                        'KE' => 'KENYA',
                                                        'KI' => 'KIRIBATI',
                                                        'KP' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF',
                                                        'KR' => 'KOREA, REPUBLIC OF',
                                                        'KW' => 'KUWAIT',
                                                        'KG' => 'KYRGYZSTAN',
                                                        'LA' => 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC',
                                                        'LV' => 'LATVIA',
                                                        'LB' => 'LEBANON',
                                                        'LS' => 'LESOTHO',
                                                        'LR' => 'LIBERIA',
                                                        'LY' => 'LIBYA',
                                                        'LI' => 'LIECHTENSTEIN',
                                                        'LT' => 'LITHUANIA',
                                                        'LU' => 'LUXEMBOURG',
                                                        'MO' => 'MACAO',
                                                        'MK' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
                                                        'MG' => 'MADAGASCAR',
                                                        'MW' => 'MALAWI',
                                                        'MY' => 'MALAYSIA',
                                                        'MV' => 'MALDIVES',
                                                        'ML' => 'MALI',
                                                        'MT' => 'MALTA',
                                                        'MH' => 'MARSHALL ISLANDS',
                                                        'MQ' => 'MARTINIQUE',
                                                        'MR' => 'MAURITANIA',
                                                        'MU' => 'MAURITIUS',
                                                        'YT' => 'MAYOTTE',
                                                        'MX' => 'MEXICO',
                                                        'FM' => 'MICRONESIA, FEDERATED STATES OF',
                                                        'MD' => 'MOLDOVA, REPUBLIC OF',
                                                        'MC' => 'MONACO',
                                                        'MN' => 'MONGOLIA',
                                                        'ME' => 'MONTENEGRO',
                                                        'MS' => 'MONTSERRAT',
                                                        'MA' => 'MOROCCO',
                                                        'MZ' => 'MOZAMBIQUE',
                                                        'MM' => 'MYANMAR',
                                                        'NA' => 'NAMIBIA',
                                                        'NR' => 'NAURU',
                                                        'NP' => 'NEPAL',
                                                        'NL' => 'NETHERLANDS',
                                                        'NC' => 'NEW CALEDONIA',
                                                        'NZ' => 'NEW ZEALAND',
                                                        'NI' => 'NICARAGUA',
                                                        'NE' => 'NIGER',
                                                        'NG' => 'NIGERIA',
                                                        'NU' => 'NIUE',
                                                        'NF' => 'NORFOLK ISLAND',
                                                        'MP' => 'NORTHERN MARIANA ISLANDS',
                                                        'NO' => 'NORWAY',
                                                        'OM' => 'OMAN',
                                                        'PK' => 'PAKISTAN',
                                                        'PW' => 'PALAU',
                                                        'PS' => 'PALESTINIAN TERRITORY, OCCUPIED',
                                                        'PA' => 'PANAMA',
                                                        'PG' => 'PAPUA NEW GUINEA',
                                                        'PY' => 'PARAGUAY',
                                                        'PE' => 'PERU',
                                                        'PH' => 'PHILIPPINES',
                                                        'PN' => 'PITCAIRN',
                                                        'PL' => 'POLAND',
                                                        'PT' => 'PORTUGAL',
                                                        'PR' => 'PUERTO RICO',
                                                        'QA' => 'QATAR',
                                                        'RE' => 'RÉUNION',
                                                        'RO' => 'ROMANIA',
                                                        'RU' => 'RUSSIAN FEDERATION',
                                                        'RW' => 'RWANDA',
                                                        'BL' => 'SAINT BARTHÉLEMY',
                                                        'SH' => 'SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA',
                                                        'KN' => 'SAINT KITTS AND NEVIS',
                                                        'LC' => 'SAINT LUCIA',
                                                        'MF' => 'SAINT MARTIN (FRENCH PART)',
                                                        'PM' => 'SAINT PIERRE AND MIQUELON',
                                                        'VC' => 'SAINT VINCENT AND THE GRENADINES',
                                                        'WS' => 'SAMOA',
                                                        'SM' => 'SAN MARINO',
                                                        'ST' => 'SAO TOME AND PRINCIPE',
                                                        'SA' => 'SAUDI ARABIA',
                                                        'SN' => 'SENEGAL',
                                                        'RS' => 'SERBIA',
                                                        'SC' => 'SEYCHELLES',
                                                        'SL' => 'SIERRA LEONE',
                                                        'SG' => 'SINGAPORE',
                                                        'SX' => 'SINT MAARTEN (DUTCH PART)',
                                                        'SK' => 'SLOVAKIA',
                                                        'SI' => 'SLOVENIA',
                                                        'SB' => 'SOLOMON ISLANDS',
                                                        'SO' => 'SOMALIA',
                                                        'ZA' => 'SOUTH AFRICA',
                                                        'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
                                                        'SS' => 'SOUTH SUDAN',
                                                        'ES' => 'SPAIN',
                                                        'LK' => 'SRI LANKA',
                                                        'SD' => 'SUDAN',
                                                        'SR' => 'SURINAME',
                                                        'SJ' => 'SVALBARD AND JAN MAYEN',
                                                        'SZ' => 'SWAZILAND',
                                                        'SE' => 'SWEDEN',
                                                        'CH' => 'SWITZERLAND',
                                                        'SY' => 'SYRIAN ARAB REPUBLIC',
                                                        'TW' => 'TAIWAN, PROVINCE OF CHINA',
                                                        'TJ' => 'TAJIKISTAN',
                                                        'TZ' => 'TANZANIA, UNITED REPUBLIC OF',
                                                        'TH' => 'THAILAND',
                                                        'TL' => 'TIMOR-LESTE',
                                                        'TG' => 'TOGO',
                                                        'TK' => 'TOKELAU',
                                                        'TO' => 'TONGA',
                                                        'TT' => 'TRINIDAD AND TOBAGO',
                                                        'TN' => 'TUNISIA',
                                                        'TR' => 'TURKEY',
                                                        'TM' => 'TURKMENISTAN',
                                                        'TC' => 'TURKS AND CAICOS ISLANDS',
                                                        'TV' => 'TUVALU',
                                                        'UG' => 'UGANDA',
                                                        'UA' => 'UKRAINE',
                                                        'AE' => 'UNITED ARAB EMIRATES',
                                                        'GB' => 'UNITED KINGDOM',
                                                        'US' => 'UNITED STATES',
                                                        'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS',
                                                        'UY' => 'URUGUAY',
                                                        'UZ' => 'UZBEKISTAN',
                                                        'VU' => 'VANUATU',
                                                        'VE' => 'VENEZUELA, BOLIVARIAN REPUBLIC OF',
                                                        'VN' => 'VIET NAM',
                                                        'VG' => 'VIRGIN ISLANDS, BRITISH',
                                                        'VI' => 'VIRGIN ISLANDS, U.S.',
                                                        'WF' => 'WALLIS AND FUTUNA',
                                                        'EH' => 'WESTERN SAHARA',
                                                        'YE' => 'YEMEN',
                                                        'ZM' => 'ZAMBIA',
                                                        'ZW' => 'ZIMBABWE'};
    global void onInstall(InstallContext context) {
        // If already on the latest version, abort the script
        if ( !beforeVersion(MAJOR_VERSION,MINOR_VERSION,PATCH_VERSION) )
            return;

            loadISOCountries();

            if (beforeVersion(1,2,0)){
                createVersionsForSurveys();
            }
        // Update the version in the system
        setVersion(MAJOR_VERSION, MINOR_VERSION, PATCH_VERSION);
    }
    private void loadISOCountries(){
        Map<String,ISO_Country__c> currentCountries  = ISO_Country__c.getAll();
        ISO_Country__c[] countriesToInsert = new ISO_Country__c[]{};
        for(string countryCode:PostInstallScript.allCountryCodes.keySet()){
            if(!currentCountries.containsKey(countryCode)){
                countriesToInsert.add(new ISO_Country__c(Name=countrycode,ShortName__c=PostInstallScript.allCountryCodes.get(countryCode)));
            }
        }
        upsert countriesToInsert;
    }


    // Create SurveyVersion__c objects for surveys created before the version model was introduced (before 1.2)
    private void createVersionsForSurveys(){


        Set<Id> idsWithVersion = new Set<Id>();
        Map<Id,String> surveyStatus = new Map<Id,String>();
        for(SurveyVersion__c sv : [SELECT Survey__c FROM SurveyVersion__c LIMIT 5000])
            idsWithVersion.add(sv.Survey__c);
        SurveyVersion__c[] newVersions = new SurveyVersion__c[]{};
        Survey__c[] surveys = [SELECT Id, Status__c, Version__c FROM Survey__c WHERE Id NOT IN :idsWithVersion];

        //save the status of the surveys,
        //and update them all in draft,to update their questions(survey must be 'Draft')
        for(Survey__c survey : surveys){
            surveyStatus.put(survey.Id,survey.Status__c);
            // If status is not Draft, version will be created by trigger when restoring original status.
            // If status is Draft, version has to be created manually.
            if (survey.Status__c=='Draft'){
                survey.Version__c = 1;
                newVersions.add(new SurveyVersion__c(Survey__c = survey.Id, Version__c = 1, Status__c = 'Draft'));
            }else{
                //set the version in 0 because when the trigger that creates the Version executes
                // the version will be 1
                survey.Version__c = 0;
            }
            survey.Status__c =  'Draft';
        }

        //update questions with version no 1
        Question__c[] questions = [select ToVersion__c,FromVersion__c,Id FROM Question__c where Survey__c in:surveys];
        for(Question__c question:questions){
            question.FromVersion__c = 1;
            question.ToVersion__c=null;
        }
        insert newVersions;
        update surveys;
        update questions;

        //update the Versions of the Surveys to their original Status
        SurveyVersion__c[] allVersions =[SELECT Id,Status__c,Survey__c FROM SurveyVersion__c WHERE Survey__c in :surveys];
        for (SurveyVersion__c ver:allVersions){
            ver.Status__c = surveyStatus.get(ver.Survey__c);
            ver.Version__c = 1;
        }
        update allVersions;

    }

    // HELPER METHOD - check if the installed version is previous to this version
    private Boolean beforeVersion(Integer major, Integer minor, Integer patch){
        Settings__c settings = Settings__c.getInstance('1');
        if (settings == null){
            settings = new Settings__c(Name = '1', MajorVersion__c = 0, MinorVersion__c = 0, PatchVersion__c = 0);
            insert settings;
        }
        return (major >  settings.MajorVersion__c)
            || (major == settings.MajorVersion__c && minor >  settings.MinorVersion__c)
            || (major == settings.MajorVersion__c && minor == settings.MinorVersion__c && patch > settings.PatchVersion__c);
    }

    // HELPER METHOD - set the version after post install
    private static void setVersion(Integer major, Integer minor, Integer patch){
        Settings__c settings = Settings__c.getInstance('1');
        if (settings == null){
            insert new Settings__c(Name = '1', MajorVersion__c = 0, MinorVersion__c = 0, PatchVersion__c = 0);
        } else {
            settings.MajorVersion__c = major;
            settings.MinorVersion__c = minor;
            settings.PatchVersion__c = patch;
            update settings;
        }
    }

    //-------------
    // TEST METHODS
    //-------------

    @isTest
    static void testInstallScript() {
        delete [SELECT Id FROM ISO_Country__c];
        PostInstallScript postinstall = new PostInstallScript();
        Test.testInstall(postinstall, null);
         Map<String,ISO_Country__c> currentcountries  = ISO_Country__c.getAll();
        System.assertEquals(false, currentcountries.isEmpty());
        System.assertEquals(PostInstallScript.allCountryCodes.size(), currentcountries.size());
    }

    @isTest
    static void testCreateVersionsForSurveys(){

         // Set current version to 1.1.0, so that the createVersions process gets executed.
        setVersion(1,1,0);

        Survey__c survey_a = new Survey__c(Name='__test__a_lapabr'); // Status will be 'Draft'
        Survey__c survey_b = new Survey__c(Name='__test__b_9n3sfb'); // Status will be 'Draft'
        Survey__c survey_c = new Survey__c(Name='__test__c_0v+vfa'); // Status will be 'Published'
        Survey__c survey_d = new Survey__c(Name='__test__d_lskn47'); // Status will be 'Closed'
        insert new Survey__c[]{survey_a, survey_b, survey_c, survey_d};

        // insert a question for a survey,and change FromVersion manually,
        // because a trigger assigns a FromVersion Automatically
        Question__c q =  new Question__c(survey__c=survey_a.Id, Caption__c= 'Username',Type__c = QuestionType.QUESTION_TEXT_SHORT);
        insert q;
        q.FromVersion__c = null;
        update q;

        //FromVersion__c should be null as initial state
        q = [SELECT Id,FromVersion__c FROM Question__c where Id = :q.Id][0];
        system.assertEquals(null,q.FromVersion__c);

        //Update the Version status so the trigger UpdateSurveyOnLastVersionUpdate update the survey status as well
        SurveyVersion__c versionPublished = [SELECT Id FROM SurveyVersion__c WHERE Survey__c = :survey_c.Id];
        versionPublished.Status__c = 'Published';
        update versionPublished;

        SurveyVersion__c[] surveyVersions = [SELECT Id, Survey__c
                                             FROM SurveyVersion__c
                                             WHERE Survey__c IN (:survey_a.Id, :survey_b.Id, :survey_c.Id, :survey_d.Id)
                                             ORDER BY Survey__r.Name];
        Map<Id, String> originalStatus = new Map<Id, String>{
            survey_a.Id => 'Draft',
            survey_b.Id => 'Draft',
            survey_c.Id => 'Published',
            survey_d.Id => 'Closed'
        };

        // Update the status of the surveys. This is done by updating the status of the version, then the trigger updates the survey.
        // Then delete the versions (the starting point is that they don't exist)
        for(SurveyVersion__c sver : surveyVersions){
            sver.Status__c = originalStatus.get(sver.Survey__c);
        }
        update surveyVersions;
        delete surveyVersions;

        // Assert that the surveys are in the correct original status
        for(Survey__c sur : [SELECT Id, Status__c FROM Survey__c
                             WHERE Id IN (:survey_a.Id, :survey_b.Id, :survey_c.Id, :survey_d.Id)
                             ORDER BY Name]){
            system.assertEquals(originalStatus.get(sur.Id), sur.Status__c, 'Survey Id: ' + sur.Id);
        }


        PostInstallScript postinstall = new PostInstallScript();
        Test.testInstall(postinstall, null);


        //update data from surveys and questions
        survey_a = [SELECT Id,Status__c,Version__c FROM Survey__c where Id = :survey_a.Id];
        survey_b = [SELECT Id,Status__c,Version__c FROM Survey__c where Id = :survey_b.Id];
        survey_c = [SELECT Id,Status__c,Version__c FROM Survey__c where Id = :survey_c.Id];
        survey_d = [SELECT Id,Status__c,Version__c FROM Survey__c where Id = :survey_d.Id];

        //question data
        q = [SELECT Id,FromVersion__c,toVersion__c FROM Question__c where id = :q.Id];
        system.assertEquals(1,q.FromVersion__c);
        system.assertEquals(null,q.ToVersion__c);

        //all versions created
        SurveyVersion__c[] versions = [SELECT Id, Survey__c, Version__c, Status__c
                                       FROM SurveyVersion__c
                                       WHERE Survey__c IN (:survey_a.Id, :survey_b.Id, :survey_c.Id, :survey_d.Id)
                                       ORDER BY Survey__r.Name ASC];
        system.assertEquals(4, versions.size());

        //Correct version survey assignment,status and version number
        system.assertEquals(survey_a.Id, versions[0].Survey__c);
        system.assertEquals(survey_b.Id, versions[1].Survey__c);
        system.assertEquals(survey_c.Id, versions[2].Survey__c);
        system.assertEquals(survey_d.Id, versions[3].Survey__c);
        system.assertEquals(originalStatus.get(survey_a.Id), survey_a.Status__c);
        system.assertEquals(originalStatus.get(survey_b.Id), survey_b.Status__c);
        system.assertEquals(originalStatus.get(survey_c.Id), survey_c.Status__c);
        system.assertEquals(originalStatus.get(survey_d.Id), survey_d.Status__c);
        system.assertEquals(originalStatus.get(survey_a.Id), versions[0].Status__c);
        system.assertEquals(originalStatus.get(survey_b.Id), versions[1].Status__c);
        system.assertEquals(originalStatus.get(survey_c.Id), versions[2].Status__c);
        system.assertEquals(originalStatus.get(survey_d.Id), versions[3].Status__c);
        system.assertEquals(1, survey_a.Version__c);
        system.assertEquals(1, survey_b.Version__c);
        system.assertEquals(1, survey_c.Version__c);
    }
}
