public with sharing class SurveyFieldMappingController {
    public Survey__c survey{get; set;}                                  // Actual survey.
    public List<QuestionLine> Questions{get;set;}
    public List<ObjectColumn> headerColumns{get;set;}                    // Object Columns(SurveyMapping__c)
    private SelectOption[] allObjs;
    public SelectOption[] objectOptions{get{return allObjs;}}            // Availables Object to choose.
    private Enviroment envVariables;                                    // Static Enviroment variables.
    public integer index{get;set;}
    private Map<integer,List<string>> requiredFieldsbyColumn;            //Contains all the requiered fields for each selected object
    public string requiredFields{get{return JSON.serialize(requiredFieldsbyColumn);}} // Returns a JSON string with all the requiered fields for each selected object
    public boolean showAdd{get;set;}
    public boolean readOnly{get;set;}
    public static final Set<String> UNMAPPABLE_TYPES = new Set<String>{ // Questions of these types cannot be mapped
        QuestionType.QUESTION_SECTION,
        QuestionType.QUESTION_STATIC};


    /*
    *    Class constructor
    */
    public SurveyFieldMappingController (ApexPages.StandardController stdController) {
        String commingFromBuilder = Apexpages.currentPage().getParameters().get('b');
        showAdd = false;
        headerColumns = new List<ObjectColumn>();
        this.survey = (Survey__c)stdController.getRecord();
        this.survey = [select Id,Name,isPPI__c,LastModifiedDate FROM Survey__c WHERE Id=:survey.id limit 1];
        // Obtain all questions and their options, for this survey.
        Question__c[] allSections = [Select Id, Name, Type__c, Parent__c, Caption__c, Hint__c, Position__c,Minimum__c, Maximum__c, DoBefore__c,
                            DoAfter__c, Required__c, SkipLogicBehavior__c, SkipLogicOperator__c, CurrentTime__c
                            From Question__c Where Survey__c = :survey.Id and Type__c =:QuestionType.QUESTION_SECTION order by Position__c asc];
        Question__c[] allQuestions = [Select Id, Name, Type__c, Parent__c, Caption__c, Hint__c, Position__c,Minimum__c, Maximum__c, DoBefore__c,
                            DoAfter__c, Required__c, SkipLogicBehavior__c, SkipLogicOperator__c, CurrentTime__c
                            From Question__c Where Survey__c = :survey.Id and Type__c !=:QuestionType.QUESTION_SECTION order by Position__c asc];
        OrderQuestionbySection(allSections,allQuestions);
        envVariables =  new Enviroment();
        //Loads the existents Object Columns(SurveyMapping__c)
        loadObjectSelectList();
        loadMappedObjects();
        LoadRequiredFields();
        string SurveyStatus = [SELECT Status__c FROM Survey__c WHERE Id=:survey.Id limit 1].Status__c;
        readOnly = !((commingFromBuilder == null || commingFromBuilder == '1') && SurveyStatus == 'Draft' && Survey__c.sObjectType.getDescribe().isUpdateable());
    }

    //-------------------------------------------------------
    //URL License Control, can't execute code without licence permission
    public PageReference checkLicense(){
       LicenseSettings__c license = C.getLicense();
       if( !license.PPI_Editable__c && this.survey.IsPPI__c )
            throw new C.LicenseException(system.label.SYSTEM_ERR_NOLICENSEPERMISSION);
       return null;
    }
    //--------------------------------------------------------
    private void loadMappedObjects(){
        SurveyMapping__c[] allSurveyMappings = [SELECT Id,MatchingField__c,ObjectApiName__c,Survey__c FROM SurveyMapping__c WHERE Survey__c=:survey.Id];
        QuestionMapping__c[] questionMappings = [SELECT Id,FieldApiName__c,Question__c,SurveyMapping__c FROM QuestionMapping__c WHERE SurveyMapping__c in :allSurveyMappings];
        Map<Id,integer> surveyMappingIdtoColumnIndex = new Map<Id,integer>();
        for(SurveyMapping__c sM:allSurveyMappings){
            surveyMappingIdtoColumnIndex.put(sM.Id,headerColumns.size());
            headerColumns.add(new ObjectColumn(sM.ObjectApiName__c,headerColumns.size(),envVariables,true,sM.Id,sM.MatchingField__c));
        }
        //Check and complete each question with the corresponding QuestionMapping
        for(QuestionLine qL:Questions){
            for(QuestionMapping__c qM:questionMappings){
                if(qL.Question.Id == qM.Question__c){
                    integer index = surveyMappingIdtoColumnIndex.get(qM.SurveyMapping__c);
                    ObjectColumn obC = headerColumns[index];
                    if(qL.columns == null)
                        qL.Columns = new ObjectColumn[headerColumns.size()];
                    qL.Columns.set(obC.index,new ObjectColumn(obC.ObjectName,obC.index,envVariables,obC.allFieldsbyType,qM.Id,qM.FieldApiName__c,qL.Question.type__c));
                }
            }
        }
        for(QuestionLine qL:Questions){
            for(ObjectColumn obC:headerColumns){
                if( isValidQuestionForMap(qL.Question.Type__c) ){
                    if(qL.Columns == null )
                        qL.Columns = new ObjectColumn[headerColumns.size()];
                    if(qL.Columns[obC.index] == null){
                        qL.Columns.set(obC.index,new ObjectColumn(obC.ObjectName,obC.index,envVariables,obC.allFieldsbyType,null,null,qL.Question.type__c));
                    }
                }
            }
        }
    }
    private boolean isValidQuestionForMap(string type){
        return !UNMAPPABLE_TYPES.contains(type.toLowerCase());
    }
    public void LoadRequiredFields(){
        requiredFieldsbyColumn = new Map<integer,List<string>>();
        //Collects all the requeried Fields for each SelectedObject
        for(ObjectColumn hCol:headerColumns){
            if(hCol.ObjectName != null && hCol.ObjectName != '' ){
                List<string> reqFields = new List<string>();
                Schema.DescribeSObjectResult pObject = envVariables.AllObjects.get(hCol.ObjectName).getDescribe();
                for(Schema.Sobjectfield f:pObject.fields.getMap().values()){
                    if(!f.getDescribe().isNillable() && !f.getDescribe().isDefaultedOnCreate() && isValidField(f))
                        reqFields.add(f.getDescribe().getName());
                }
                requiredFieldsbyColumn.put(hCol.index,reqFields);
            }
        }
    }

    private void OrderQuestionbySection(Question__c[] lstSections,Question__c[] lstQuestions){
        Map<String,List<QuestionLine>> questionsbySectionId = new Map<String,List<QuestionLine>> ();
        //index the questions by Section
        for(Question__c quest:lstQuestions){
            List<QuestionLine> qLst= questionsbySectionId.get(quest.Parent__c);
            if(qLst == null){
                questionsbySectionId.put(quest.Parent__c,new List<QuestionLine>{new QuestionLine(quest)});
            }else{
                qLst.add(new QuestionLine(quest));
            }
            if(isValidQuestionForMap(quest.Type__c))
                showAdd = true;
        }
        //index all the sections and question in order.
        Questions = new List<QuestionLine>();
        for(Question__c section:lstSections){
            Questions.add(new QuestionLine(section));
            if(questionsbySectionId.get(section.id) != null)
                Questions.addAll(questionsbySectionId.get(section.id));
        }
    }

    public void loadObjectSelectList(){
        List<Schema.sObjectType> objts = envVariables.AllObjects.values();
        allObjs = new SelectOption[]{};
        allObjs.add(new SelectOption('','-'));
        for(Schema.sObjectType o:objts){
            allObjs.add(new SelectOption(o.getDescribe().getName(),o.getDescribe().getLabel()));
        }
    }
    public void addHeadColumn(){
        index = null;
        if(headerColumns.size() == 0 || headerColumns.get(headerColumns.size() - 1).ObjectName != null){
            headerColumns.add(new ObjectColumn(null,headerColumns.size(),envVariables,true,null,null));
            loadNewFieldsColumns();
        }else{
            if(headerColumns.get(headerColumns.size() - 1).isDeleted){
                headerColumns.get(headerColumns.size() - 1).isDeleted = false;
                loadNewFieldsColumns();
            }
        }
        }
    public void removeHeadColumn(){
        headerColumns[index].isDeleted = true;
        for(QuestionLine q:Questions){
            if(isValidQuestionForMap(q.Question.type__c))
                q.Columns[index].isDeleted = true;
        }
        index = null;
    }
    public void changeObject(){
        headerColumns[index].MatchingField = null;
        loadNewFieldsColumns();
        LoadRequiredFields();
    }
    public void loadNewFieldsColumns(){
        for(QuestionLine q:Questions){
            for(ObjectColumn obC:headerColumns){
                //Only update the FieldColumn related to the selected index or if the Fieldcolumn only have one value('-')
                if (obc.index == index || obC.ObjectName == null){
                    if( isValidQuestionForMap(q.Question.type__c) ){
                        if(q.columns == null)
                            q.Columns = new List<ObjectColumn>();
                        if(q.columns.size() <= obC.index)
                            q.Columns.add(new ObjectColumn(obC.ObjectName,obC.index,envVariables,obC.allFieldsbyType,null,null,q.Question.Type__c));
                        else
                            q.Columns.set(obC.index,new ObjectColumn(obC.ObjectName,obC.index,envVariables,obC.allFieldsbyType,null,null,q.Question.Type__c));
                    }
                }
            }
        }
    }
    public void save(){
        //Create Savepoint to be able to rollback all the DB changes if needed
        Savepoint sp = Database.setSavepoint();
        //The head columns will be the surveyMapping Object
        Survey__c currentSurvey = [select Id, Status__c, LastModifiedDate from Survey__c where Id = :this.survey.Id limit 1];
        if(currentSurvey.LastModifiedDate != survey.LastModifiedDate){
            ApexPages.message myMsg = new ApexPages.Message(ApexPages.Severity.ERROR, system.Label.BUILDER_ERR_CONCURRENTSAVE);
            ApexPages.addMessage(myMsg);
        }else if(currentSurvey.Status__c != 'Draft'){
            ApexPages.message myMsg = new ApexPages.Message(ApexPages.Severity.ERROR, system.Label.BUILDER_ERR_SURVEYALREADYPUBLISHED);
            ApexPages.addMessage(myMsg);
        }else if(readOnly){
            ApexPages.message myMsg = new ApexPages.Message(ApexPages.Severity.ERROR, system.Label.BUILDER_ERR_READONLYSURVEY);
            ApexPages.addMessage(myMsg);
        }else{
            if(headerColumns.size() != 0){
                if(validateRequiredFields()){
                    update survey;
                    this.survey = [select Id,Name,isPPI__c,LastModifiedDate FROM Survey__c WHERE Id=:survey.id limit 1];
                    SurveyMapping__c[] surveyMappingLst = new SurveyMapping__c[]{};
                    Map<integer,SurveyMapping__c> colIndexToSurveyMapping = new Map<integer,SurveyMapping__c>();

                    // For each column on the page, create it's SurveyMapping object
                    SurveyMapping__c[] SurveyMappingtoDelete = new SurveyMapping__c[]{};
                    for(ObjectColumn column:headerColumns){
                        if(column.isDeleted && column.ObjectId != null){
                            SurveyMapping__c sM = new SurveyMapping__c(Id = column.ObjectId);
                            SurveyMappingtoDelete.add(sM);
                        }
                        if(column.ObjectName != null && column.ObjectName != '' && !column.isDeleted){
                            SurveyMapping__c sM = new SurveyMapping__c(
                                    Id = column.ObjectId,
                                    ObjectApiName__c = column.ObjectName,
                                    MatchingField__c = column.MatchingField,
                                    Survey__c = survey.Id);
                            colIndexToSurveyMapping.put(column.index,sM);
                            surveyMappingLst.add(sM);
                        }
                    }
                    delete SurveyMappingtoDelete;
                    upsert surveyMappingLst;

                    //Store the SurveyMapping Id in the proper HeadColumn
                    for(ObjectColumn column:headerColumns){
                        if(column.ObjectName != null && column.ObjectName != '' && !column.isDeleted){
                            column.setObjectId(colIndexToSurveyMapping.get(column.index).Id);
                        }
                    }

                    //Delete all the existing questionMappings for these SurveyMappings
                    delete [SELECT Id FROM QuestionMapping__c WHERE SurveyMapping__c in :surveyMappingLst];

                    // Now check each question and create the necessary Question mappings Objects
                    QuestionMapping__c[] questionMappingLst = new QuestionMapping__c[]{};
                    Set<Question__c> questionsToMakerequired = new Set<Question__c>();
                    for(QuestionLine question:Questions){
                        //Creates one QuestionMapping__c per column
                        if( isValidQuestionForMap(question.Question.Type__c) ){
                            for(ObjectColumn column:question.Columns){
                                if(column.SelectedField != null && column.SelectedField != '' && !column.isDeleted){
                                    Schema.DescribeSObjectResult pObject = envVariables.AllObjects.get(column.ObjectName).getDescribe();
                                    if(!pObject.fields.getMap().get(column.SelectedField).getDescribe().isNillable()
                                        && !questionsToMakerequired.contains(question.question)){
                                        question.question.Required__c = true;
                                        questionsToMakerequired.add(question.question);
                                    }
                                    Id surveyMappingId = colIndexToSurveyMapping.get(column.index).Id;
                                    questionMappingLst.add(
                                        new QuestionMapping__c(
                                            FieldApiName__c = column.SelectedField,
                                            SurveyMapping__c = surveyMappingId,
                                            Question__c = question.Question.Id)
                                    );
                                }
                            }
                        }
                    }
                    List<Question__c> questionrequired = new List<Question__c>();
                    questionrequired.addAll(questionsToMakerequired);
                    update questionrequired ;
                    try{
                        upsert questionMappingLst;
                    }catch(Exception e){
                        Apexpages.addMessages(e);
                        Database.rollback(sp);
                        return;
                    }
                    //Remove the deleted columns
                    List<integer> headerColumnsToRemove = new List<integer>();
                    for(SurveyMapping__c sM:SurveyMappingtoDelete){
                        for(ObjectColumn column:headerColumns){
                            if(sM.Id == column.ObjectId)
                                headerColumnsToRemove.add(column.Index);
                        }
                    }
                    for(integer i :headerColumnsToRemove){
                        headerColumns.remove(i);
                    }
                }
            }
        }
    }

    public PageReference saveandNext(){
        save();
        if(!ApexPages.hasMessages(ApexPages.Severity.FATAL) && !ApexPages.hasMessages(ApexPages.Severity.ERROR)){
            PageReference preview = Page.SurveyBuilderPreview;
            preview.getParameters().put('id',survey.Id);
           return preview;
        }else{
            return null;
        }
    }
    /**
     * This method checks if all the required fields for the selected objects are mapped to a question
     * if yes it returns true, if not it returns false and adds a ApexMessage for each required field is not mapped.
    **/
    private boolean validateRequiredFields(){
        Map<integer,Set<string>> requiredFieldsbyColumn = new Map<integer,Set<string>>();
        //Collects all the requeried Fields for each SelectedObject
        for(ObjectColumn hCol:headerColumns){
            if(hCol.ObjectName != null && hCol.ObjectName != '' && !hCol.isDeleted ){
                Set<string> reqFields = new Set<string>();
                Schema.DescribeSObjectResult pObject = envVariables.AllObjects.get(hCol.ObjectName).getDescribe();
                for(Schema.Sobjectfield f:pObject.fields.getMap().values()){
                    if(!f.getDescribe().isNillable() && !f.getDescribe().isDefaultedOnCreate() && isValidField(f))
                        reqFields.add(f.getDescribe().getName());
                }
                requiredFieldsbyColumn.put(hCol.index,reqFields);
            }
        }
        //Iterates each question and checks if the required fields collected before have some question assigned
        for(QuestionLine q:Questions){
            if( isValidQuestionForMap(q.Question.type__c) ){
                for(ObjectColumn quesCol:q.Columns){
                    Set<string> reqFields = requiredFieldsbyColumn.get(quesCol.index);
                    if(reqFields != null && reqFields.contains(quesCol.SelectedField))
                        reqFields.remove(quesCol.SelectedField);

                }
            }
        }
        boolean isValid = true;
        //Checks each required field not assigned to a question and creates an error message to display in the page.
        for(ObjectColumn hCol:headerColumns){
            List<string> reqFields = new List<string>();
            if(requiredFieldsbyColumn.get(hCol.index) != null)
                reqFields.addAll(requiredFieldsbyColumn.get(hCol.index));
            if(reqFields.size() > 0){
                Apexpages.addMessage(new ApexPages.Message(ApexPages.Severity.FATAL, 'The following fields are required for ' + hCol.ObjectName + ' Object: ' +reqFields));
                isValid = false;
            }
        }
        if(!isValid){
            Apexpages.addMessage(new ApexPages.Message(ApexPages.Severity.FATAL,'The missing Field could be unavailable if there is no Question that matches its type.'));
        }
        return isValid;
    }
    //Returns true is the field is valid for object mapping.
    private static boolean isValidField(Schema.Sobjectfield f){
        return (f.getDescribe().isUpdateable() && f.getDescribe().isCreateable() && f.getDescribe().getName() != 'OwnerId' );
    }
    /*
     * inner Classes
     */
    public class ObjectColumn{

        //private variables
        private SelectOption[] pfieldLst;
        private integer pIndex;
        private Id pObjectId;
        private Map<string,SelectOption[]> pAllFieldsbyType;
        private boolean isHeader = false;
        private Enviroment env;

        //public properties
        public Id ObjectId{get{return pObjectId;}}
        public string SelectedField{get;set;}
        public integer index{get{return pIndex;}}
        public SelectOption[] FieldList{get{return pfieldLst;}}
        public Map<string,SelectOption[]> allFieldsbyType{get{return pAllFieldsbyType;}}
        public string MatchingField{get;set;}
        public boolean isDeleted{get;set;}
        public string ObjectName{get;set{
            //Each time I set/change the Selected ObjectName for a head column update the related Field List
            ObjectName = value;
            if(isHeader)
                getFieldsbyType();
        }}

        //This "set" method is defined like this to avoid writing from the page.
        public void setObjectId(Id objectId){
            pObjectId = objectId;
        }

        //-------------------------------------------- Contructors --------------------------------------------
        /*This contructor will be used to create the Head Columns.
         */
        public ObjectColumn(String oName,Integer ColIndex,Enviroment envVar,boolean isHeader, string survMappingId,string MatchField){
            this.isHeader = isHeader;
            env = envVar;
            ObjectName = oName;
            pIndex = ColIndex;
            pObjectId = survMappingId;
            this.MatchingField = MatchField;
            isDeleted = false;
        }
        /*Overload Contructor, the parameter fLst is past from the already existing ObjectColumn from headerColumns(Head Columns collection)
         */
        public ObjectColumn(String oName,Integer index,Enviroment envVar,Map<string,SelectOption[]> fLst,string quesMappingId,string selField,string Type){
            env = envVar;
            ObjectName = oName;
            pIndex = index;
            pAllFieldsbyType = fLst;
            this.SelectedField = selField;
            pObjectId = quesMappingId;
            pfieldLst =  new SelectOption[]{new SelectOption('','-')};
            getFieldList(Type);
            isDeleted = false;
        }
        //-------------------------------------------- End Contructors --------------------------------------------
        //-------------------------------------------- Private methods --------------------------------------------
        //This method collect all the fields for the selected object and order thems by data type.
        private void getFieldsbyType(){
            pAllFieldsbyType = new Map<string,SelectOption[]>();
            if(ObjectName != null && ObjectName != ''){
                Schema.DescribeSObjectResult pObject = env.AllObjects.get(ObjectName).getDescribe();
                for(Schema.Sobjectfield f:pObject.fields.getMap().values()){
                    if(isValidField(f)){
                        string fType = f.getDescribe().getType().name();
                        SelectOption[] fLst = pAllFieldsbyType.get(fType);
                        SelectOption sOp = new SelectOption(f.getDescribe().getName(),f.getDescribe().getLabel());
                        if(fLst == null){
                            pAllFieldsbyType.put(fType,new SelectOption[]{sOp});
                        }else{
                            fLst.add(sOp);
                        }
                    }
                }
            }
        }
        //This method returns the Fields for the selected object that are compatible with the question type
        private void getFieldList(string qType){
            if(pAllFieldsbyType.size() != 0){
                string fType = QuestionType.QuestionTypeToSchemaType.get(qType);
                SelectOption[] tAreaFields = pAllFieldsbyType.get(Schema.Displaytype.TextArea.name());
                if(tAreaFields != null)
                    pfieldLst.addAll(tAreaFields);
                if(qType != QuestionType.QUESTION_TEXT_LONG){
                    SelectOption[] stringFields = pAllFieldsbyType.get(Schema.Displaytype.String.name());
                    if(stringFields != null)
                        pfieldLst.addAll(stringFields);
                    SelectOption[] questionTypeFields = pAllFieldsbyType.get(fType);
                    if(questionTypeFields != null)
                        pfieldLst.addAll(questionTypeFields);
                }
            }
        }
    }
    public class Enviroment{
        private Map<String, Schema.SObjectType> allObj;
        public Map<String, Schema.SObjectType> AllObjects{get{return allObj;}}
        public Enviroment(){
            allObj    = Schema.getGlobalDescribe();
        }
    }

    public class QuestionLine{
        public ObjectColumn[] Columns{get;set;}
        public Question__c Question{get;set;}
        public String Caption{get{return Question.Caption__c;}}
        public QuestionLine(Question__c q){
            Question = q;
        }
    }

}