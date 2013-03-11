public with sharing class XFormEditor {

    public Attachment actual_doc{ get; set; }
    public String body_actual_doc_val {get; set;}
    public String save_type {get;set;}
    public String Message{get; set; }
    public Survey__c survey {get;set;}
    public boolean hasErrors {get;set;}
    public boolean isEditable {get;set;}

    public XFormEditor(){}

    // This method shows the document related to the selected Survey in a separate page.
    public PageReference editXForm(){
        hasErrors = false;
        PageReference pg = new PageReference('/apex/SurveyManager');
        Id auxSurveyId = Apexpages.currentPage().getParameters().get('surveyId');

        if( auxSurveyId != null ){
            List<Survey__c> listSur = [select Name,Alias__c,Id, isPPI__c, Status__c,Gps_Location_Enabled__c, Close_Message__c,InterviewMapField__c, Gps_ShowMap__c From Survey__c where Id = :auxSurveyId];
            if( listSur != null && !listSur.isEmpty()) {
                survey = listSur[0];
                //-------------------------------------------------------        
                //URL License Control, can't execute code without licence permission
                LicenseSettings__c license = C.getLicense();
                if( !license.PPI_Editable__c && survey.IsPPI__c )
                    throw new C.LicenseException(system.label.SYSTEM_ERR_NOLICENSEPERMISSION);
                //--------------------------------------------------------
                

                    isEditable = (survey.Status__c == 'DRAFT');
                    List<Attachment> listDoc = [select Id, Body from Attachment where ParentId = :survey.Id and Name = :XformCreator.XFORM_ATTACHMENT ];

                    //if the document does not exist, creates a new one
                    if( listDoc == null || listDoc.isEmpty()){
                        new XFormCreator().createDocument(survey);
                        listDoc = [select Id, Body from Attachment where ParentId = :survey.Id and Name = :XformCreator.XFORM_ATTACHMENT ];
                    }

                    this.actual_doc = listDoc[0];
                    Blob blobDoc = this.actual_doc.Body;
                    String docBodyStr = blobDoc.toString();
                    this.Message = docBodyStr;
                    pg = null;
            }
        }
        return pg;
    }

    // saves changes. if it has an error, stays in the page and displays it. if it is a "save" return to survey manager, if it is a "quicksave" stays in the page
    public PageReference save () {

        if(body_actual_doc_val != null){
            this.actual_doc.Body = Blob.valueof(body_actual_doc_val);
            update this.actual_doc;

            //update the XForm Status in the version
            List<Survey__c> listSur = [select Id, XForm_Status__c, Saving_XForm__c From Survey__c where Id = :this.actual_doc.ParentId];
            if( listSur != null && !listSur.isEmpty()) {
                Survey__c survey = listSur[0];
                survey.XForm_Status__c = 'CUSTOM';
                survey.Saving_XForm__c = true;
                update survey;
            }

            hasErrors = false;
            if (this.save_type == 'save') return Page.SurveyManager;
        }
        return null;
    }

    //goes back to survey manager
    public PageReference cancel(){
        return Page.SurveyManager;
    }


}