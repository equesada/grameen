@isTest
private class XFormEditorTest {

     static testMethod void testEditXForm(){
        LicenseSettings__c testWithLicense = newLicense(false,'Test1');
        insert testWithLicense;
        
        XFormEditor x = new XFormEditor();       
        
        Survey__c survey = new Survey__c(Name='name', Status__c = 'Draft');
        insert survey;

        insert new Attachment(
                Name = ServicesController.SURVEY_ANSWER_ATTACHMENT_NAME,
                ParentId = survey.Id,
                Body = Blob.valueOf('testStringToBlob')
                );

        // Data ok
        ApexPages.currentPage().getParameters().put('surveyId', survey.Id);

        PageReference pg_test = x.editXForm();
        system.assert( pg_test == null );


        // test survey with no document
        List<Attachment> theDoc = [SELECT Id FROM Attachment where ParentId = :survey.Id and Name = :ServicesController.SURVEY_ANSWER_ATTACHMENT_NAME ];
        delete theDoc[0];
        ApexPages.currentPage().getParameters().put('surveyId', survey.Id);
        pg_test = x.editXForm();
        system.assertEquals(x.isEditable,true);
        system.assert( pg_test == null );



        //Published Survey
        survey.Status__c = 'Published';
        update survey;
        PageReference page = new PageReference('/apex/SurveyManager');
        page = x.editXForm();
        system.assertEquals(x.isEditable,false);

        //Closed Survey
        survey.Status__c = 'Closed';
        update survey;
        page = new PageReference('/apex/SurveyManager');
        pg_test = x.editXForm();
        system.assertEquals(x.isEditable,false);

        //Deprecated Survey
        survey.Status__c = 'Deprecated';
        update survey;
        page = new PageReference('/apex/SurveyManager');
        pg_test = x.editXForm();
        system.assertEquals(x.isEditable,false);

        // test cancel method (returns to the surveymanager page)
        system.assert( x.cancel().getUrl() == page.getUrl() );

        // surveyId null
        ApexPages.currentPage().getParameters().put('surveyId', null);
        pg_test = x.editXForm();
        system.assert( pg_test.getUrl() == page.getUrl() );

        // No Survey
        delete survey;
        pg_test = x.editXForm();
        system.assert( pg_test.getUrl() == page.getUrl() );

    }

    static testMethod void testSave(){
        LicenseSettings__c testWithLicense = newLicense(false,'Test1');
        insert testWithLicense;
        
        XFormEditor x = new XFormEditor();
        x.body_actual_doc_val = null;
        x.save_type = 'save';

        Survey__c survey = new Survey__c(Name='name', Status__c = 'Draft');
        insert survey;

        Attachment att = new Attachment(
                Name = XFormCreator.XFORM_ATTACHMENT,
                ParentId = survey.Id,
                Body = Blob.valueOf('testStringToBlob'));
        insert att;

        // Null XForm body
        PageReference pg_test = x.save();
        system.assert( pg_test == null );

        // Data ok
        PageReference pg = new PageReference('/apex/SurveyManager');
        x.actual_doc = att;
        x.body_actual_doc_val = att.Body.toString();
        pg_test = x.save();
        system.assert( pg_test.getUrl() == pg.getUrl() );
    }
    static testMethod void checkLicense(){
        ISO_Country__c countryCode = new ISO_Country__c(Name='AA',ShortName__c='Test');
        insert countryCode;
        Survey__c survey = new Survey__c(IsPPI__c=true,CountryCode__c='AA',PPIDataYear__c = 2103,Status__c = 'Draft');
        insert survey;
        //Without license
        LicenseSettings__c testLicense = newLicense(false,'Test');
        insert testLicense;
        try{
        	ApexPages.currentPage().getParameters().put('surveyId', survey.Id);
        	XFormEditor xF = new XFormEditor();
        }catch(Exception e){
            system.assertEquals( system.label.SYSTEM_ERR_NOLICENSEPERMISSION, e.getMessage());   
        }
        //With license
        LicenseSettings__c testWithLicense = newLicense(true,'Test');
        insert testWithLicense;
	    ApexPages.currentPage().getParameters().put('surveyId', survey.Id);
	    XFormEditor xF = new XFormEditor();
    }
    //This method delete de original license and create a new one 
    static LicenseSettings__c newLicense(Boolean PPIEditable, String licName){
        list<LicenseSettings__c> lS = [SELECT PPI_Editable__c FROM LicenseSettings__c limit 1];
        delete lS;
        LicenseSettings__c li = new LicenseSettings__c(PPI_Editable__c = PPIEditable, Name = licName);
        return li;
    }
    
}