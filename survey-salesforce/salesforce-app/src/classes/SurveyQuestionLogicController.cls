public with sharing class SurveyQuestionLogicController {

    public Survey__c survey{get; set;}
    public String nonLogicQuestions{get{return JSON.serialize(pNonLogicQuestions);}}
    private Map<Id,Question__c> pNonLogicQuestions;
    // ERROR MESSAGES
    public static final String ERR_UNKNOWN_QUESTION = 'Could not find question with id "{0}"';
    public static final String ERR_SURVEY_ALREADY_PUBLISHED= 'The survey has been published already, you cannot save it.';
    public BuilderQuestion[] questions = new BuilderQuestion[]{}; // Contains questions to be upserted on the next "save" call.
    public String getQuestions(){ return JSON.serialize(questions); }
    public String SurveyAlreadyPublished{get{return ERR_SURVEY_ALREADY_PUBLISHED;}}
    private DateTime lastModified;
    public static final String FIRST_SECTION='1'; //Constant to handle the First Section of the page

    // Maps to keep correspondence between builder objects and actual Salesforce objects.
    private Map<String,Question__c> sfquestions = new Map<String,Question__c>();
    private Map<String,Option__c> sfoptions = new Map<String,Option__c>();
    private Map<String,SkipCondition__c> sfskipconditions = new Map<String, SkipCondition__c>();

    //CONSTRUCTOR
    public SurveyQuestionLogicController(ApexPages.StandardController stdController) {
        this.survey = (Survey__c)stdController.getRecord();
        this.survey.IsPPI__c = [SELECT IsPPI__c FROM Survey__c WHERE Id=:survey.Id].IsPPI__c;
        
        // Obtain all questions and their options, for this survey.
        for(Question__c q: [Select Id, Name, Type__c, Parent__c, Caption__c, Hint__c, Position__c,
                            Minimum__c, Maximum__c, DoBefore__c, DoAfter__c, Required__c, SamePage__c,
                            CurrentTime__c, SkipLogicBehavior__c, SkipLogicOperator__c
                            From Question__c Where Survey__c = :survey.Id]){
            sfquestions.put(q.Id, q);
            questions.add( new BuilderQuestion(q) );
        }
        //Obtain all none logical Questions
        pNonLogicQuestions =new Map<id,Question__c>([Select Id FROM Question__c WHERE Survey__c = :survey.Id and Type__c =:QuestionType.QUESTION_STATIC]);

        for(Option__c o: [Select Id, Name, Position__c, Question__c,Caption__c
                          From Option__c Where Question__r.Survey__c = :survey.Id order by Position__c]){
            sfoptions.put(o.Id, o);
            findQuestion(o.Question__c).options.add( new BuilderQuestion.Option(o) );
        }

        for(SkipCondition__c c: [Select Id, Parent__c, SourceQuestion__c, Condition__c, Negate__c, Value__c
                                 From SkipCondition__c where Parent__r.Survey__c = :survey.Id]){
            sfskipconditions.put(c.Id, c);
            findQuestion(c.Parent__c).skipconditions.add(new BuilderQuestion.SkipCondition(c));
        }
        Survey__c currentSurvey = [SELECT LastModifiedDate FROM Survey__c WHERE Id=:this.survey.id limit 1];
        lastModified = currentSurvey.LastModifiedDate;
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
    // HELPER METHODS : GENERAL

    // Find a question on the list by it's id
    private BuilderQuestion findQuestion(String id) {
        for (BuilderQuestion q : questions)
            if (q.id == id)
                return q;
        return null;
    }

    // PUBLIC METHODS TO HANDLE SURVEY QUESTIONS

    // Looks for the corresponding Question__c object.
    private Question__c getQuestion(BuilderQuestion bq) {
        Question__c q = sfquestions.get(bq.id);
        return q;
    }

    // Shorthand method
    private String getParam(String key){ return ApexPages.CurrentPage().getParameters().get(key); }

    // Saves the list of questions to the database
    public PageReference save(){
        Survey__c currentSurvey = [SELECT Status__c,LastModifiedDate FROM Survey__c WHERE Id =: survey.Id];
        if(currentSurvey.Status__c != 'Draft'){
            ApexPages.message myMsg = new ApexPages.Message(ApexPages.Severity.ERROR, system.label.BUILDER_ERR_SURVEYALREADYPUBLISHED);
            ApexPages.addMessage(myMsg);
            return null;
        }else if(currentSurvey.LastModifiedDate != lastModified){
            ApexPages.message myMsg = new ApexPages.Message(ApexPages.Severity.ERROR, system.Label.BUILDER_ERR_CONCURRENTSAVE);
            ApexPages.addMessage(myMsg);
            return null;
        }
        update currentSurvey;
        currentSurvey = [SELECT LastModifiedDate FROM Survey__c WHERE Id=:this.survey.id limit 1];
        lastModified = currentSurvey.LastModifiedDate;
        String pquestions = getParam('questions');
        String pquicksave = getParam('quicksave');

        JSONParser parser = JSON.createParser(pquestions);
        //Type qarrType = Type.forName('SurveyQuestionLogicController.BuilderQuestionArray');
        BuilderQuestion[] newQuestions = (BuilderQuestion[])parser.readValueAs(BuilderQuestion[].class);

        // Delete all existing conditions
        delete [select Id from SkipCondition__c where Parent__r.Survey__c = :survey.Id];

        // Create all conditions again
        SkipCondition__c[] conditions = new SkipCondition__c[]{};
        Question__c[] questions = new Question__c[]{};
        for(BuilderQuestion bq : newQuestions){
            Question__c question = getQuestion(bq);
            bq.updateQuestion(question);
            questions.add(question);
            for(BuilderQuestion.SkipCondition cond : bq.skipconditions)
                conditions.add( cond.createSkipCondition(question) );
        }
        insert conditions;
        update questions;


        PageReference goPreview = Page.SurveyFieldMapping;
        goPreview.setRedirect(true);
        goPreview.getParameters().put('id', survey.Id);
        return pquicksave == 'true' ? null : goPreview ;
    }
}