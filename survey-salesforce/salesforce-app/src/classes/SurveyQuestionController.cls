public with sharing class SurveyQuestionController {

    public Survey__c survey{get; set;}
    public List<Question__c> questionList = new List<Question__c>();
    public Boolean positionsWereFixed {get;set;}
    
    private DateTime lastModified;
    public SurveyQuestionController(ApexPages.StandardController stdController) {
        this.survey = (Survey__c)stdController.getRecord();
        this.survey = [SELECT Id, Name, Version__c, IsPPI__c FROM Survey__c WHERE Id=:survey.Id];
        
        // Obtain all questions and their options, for this survey.
        questionList = [SELECT Id, Name, Survey__c, Type__c, Parent__c, Caption__c, Hint__c, Position__c,
                            Minimum__c, Maximum__c, DoBefore__c, DoAfter__c, Required__c, SamePage__c,
                            CurrentTime__c, SkipLogicBehavior__c, SkipLogicOperator__c
                            FROM Question__c
                            WHERE Survey__c = :survey.Id AND IsDraft__c = 1
                            ORDER BY Survey__r.Id, Parent__r.Id, Position__c];
        // Obtain all questions and their options, for this survey.
        for(Question__c q: questionList){
            sfquestions.put(q.Id, q);
            questions.add( new BuilderQuestion(q) );
        }

        for(Option__c o: [SELECT Id, Name,Caption__c, Position__c, Question__c
                          FROM Option__c
                          WHERE Question__r.Survey__c = :survey.Id AND Question__r.IsDraft__c = 1
                          ORDER BY Position__c]){
            sfoptions.put(o.Id, o);
            BuilderQuestion bq = findQuestion(o.Question__c);
            bq.options.add( new BuilderQuestion.Option(o) );
        }

        for(SkipCondition__c c: [SELECT Id, Parent__c, SourceQuestion__c, Condition__c, Negate__c, Value__c
                                 FROM SkipCondition__c
                                 WHERE Parent__r.Survey__c = :survey.Id AND Parent__r.IsDraft__c = 1]){
            sfskipconditions.put(c.Id, c);
            findQuestion(c.Parent__c).skipconditions.add(new BuilderQuestion.SkipCondition(c));
        }
        for(ScoreValue__c sv : [SELECT Id, Value__c, Option__c, Option__r.Question__c, ScoringGroup__r.Caption__c
                                FROM ScoreValue__c
                                WHERE ScoringGroup__r.Survey__c = :survey.Id AND Option__r.Question__r.IsDraft__c = 1
                                ORDER BY Id]){
            BuilderQuestion bq = findQuestion(sv.Option__r.Question__c);
            BuilderQuestion.ScoreValue newSV = new BuilderQuestion.ScoreValue(sv);
            bq.getOption(sv.Option__c).scoreValues.add(newSV);
        }
        Survey__c currentSurvey = [SELECT LastModifiedDate FROM Survey__c WHERE Id=:this.survey.id LIMIT 1];
        lastModified = currentSurvey.LastModifiedDate;

    }
    //-------------------------------------------------------        
    //URL License Control, can't execute code without licence permission
    public PageReference init(){
       LicenseSettings__c license = C.getLicense();
       if( !license.PPI_Editable__c && this.survey.IsPPI__c )
            throw new C.LicenseException(system.label.SYSTEM_ERR_NOLICENSEPERMISSION);

        Question__c prevQuestion;
        for(Question__c q : questionList){
            // Must have at least two questions 
            if(prevQuestion != null ){
                if(q.Survey__c == prevQuestion.Survey__c && q.Parent__c == prevQuestion.Parent__c ){
                    //Fixing when positions are repeated
                    if(q.Position__c == prevQuestion.Position__c || q.Position__c != prevQuestion.Position__c +1){
                        //fixing question position
                        q.Position__c = prevQuestion.Position__c + 1;
                        //fixing builder question position
                        findQuestion(q.Id).position = Integer.valueOf(q.Position__c);
                        positionsWereFixed = true;
                        //information message
                        ApexPages.addMessage(new ApexPages.Message(ApexPages.Severity.INFO, system.label.QUESTION_ERR_WRONGORDER));
                    }
                }else{
                    q.Position__c = 1;
                }
                
            //If is the first question of the list    
            }else{
                q.Position__c = 1;
            }    
            prevQuestion = q;
        }
        //Updating all questions after changes
        update questionList;
       return null; 
    }
    //--------------------------------------------------------
    // ERROR MESSAGES
    public static final String ERR_DUPLICATE_ID = 'A question with id "{0}" already exists.';
    public static final String ERR_DUPLICATE_NAME = 'A question with name "{0}" already exists.';
    public static final String ERR_UNKNOWN_QUESTION = 'Could not find question with id "{0}"';
    public static final String ERR_SURVEY_ALREADY_PUBLISHED= 'The survey has been published already, you cannot save it.';
    public BuilderQuestion[] questions        = new BuilderQuestion[]{}; // Contains questions to be upserted on the next "save" call.
    public BuilderQuestion[] deletedQuestions = new BuilderQuestion[]{}; // Contains questions to be delete on the next "save" call.
    public String error{ get; set; }
    public String getQuestions(){ return JSON.serialize(questions); }
    public String SurveyAlreadyPublished{get{return ERR_SURVEY_ALREADY_PUBLISHED;}}

    public static final String FIRST_SECTION='1'; //Constant to handle the First Section of the page

        // Maps to keep correspondence between builder objects and actual Salesforce objects.
    private Map<String,Question__c> sfquestions = new Map<String,Question__c>();
    private Map<String,Option__c> sfoptions = new Map<String,Option__c>();
    private Map<String,SkipCondition__c> sfskipconditions = new Map<String, SkipCondition__c>();

    // HELPER METHODS : GENERAL

    // Find a question on the list by it's id
    public BuilderQuestion findQuestion(String id) {
        for (BuilderQuestion q : questions)
            if (q.id == id)
                return q;
        return null;
    }

    // Find a question on the list by it's name
    public BuilderQuestion findQuestionByName(String name) {
        for (BuilderQuestion q : questions)
            if (q.name == name)
                return q;
        return null;
    }

    // PUBLIC METHODS TO HANDLE SURVEY QUESTIONS

    // Adds a new question to the list of questions
    // Returns the Id for the created question, for future reference
    public String addQuestion(String json){
        BuilderQuestion newQuestion = BuilderQuestion.fromJson(json, true);

        // Check for a duplicate name or id
        BuilderQuestion existingQuestion = null;
        existingQuestion = findQuestion(newQuestion.id);
        if (existingQuestion != null) SurveyException.raise(ERR_DUPLICATE_ID, newQuestion.id);
        existingQuestion = findQuestionByName(newQuestion.name);
        if (existingQuestion != null) SurveyException.raise(ERR_DUPLICATE_NAME, newQuestion.name);

        questions.add(newQuestion);

        // Update position after add the new question
        for(BuilderQuestion q : questions){
            if(newQuestion.type != 'section'){
                if(q.type != 'section' && q.position >= newQuestion.position && q.id != newQuestion.id && q.parent == newQuestion.parent)
                    q.position++;
            }
            else{
                if(q.type == 'section' && q.position >= newQuestion.position && q.id != newQuestion.id)
                    q.position++;
            }
        }
        return newQuestion.id;
    }

    // Edit's an existing question
    public void editQuestion(String json){
        BuilderQuestion question = BuilderQuestion.fromJson(json, false);
        BuilderQuestion existingQuestion = findQuestion(question.id);

        if (existingQuestion == null)
            // You cannot edit a question that does not exist
            SurveyException.raise(ERR_UNKNOWN_QUESTION, question.id);
        else if (existingQuestion.name != question.name){
            // If the name of the question changed, check for colisions
            for(BuilderQuestion q : questions){
                if(q.name == question.name)
                    SurveyException.raise(ERR_DUPLICATE_NAME, question.name);
            }
        }

        // Copy the new values into the existing question
        question.copyTo(existingQuestion);
    }

    // Removes a question to the survey (moves it to the "deleted questions" array.
    public void removeQuestion(String id){

        // Find the question that I want to remove
        Integer pos = 0;
        BuilderQuestion question = null;
        for(pos = 0; question == null && pos < questions.size(); pos++){
                question = questions[pos];
                if (question.id != id) question = null;
        }

        if (question == null)
                SurveyException.raise(ERR_UNKNOWN_QUESTION, id);

        // Remove this question from question list
        pos--;
        questions.remove(pos);

        // Add this question to deleted questions list
        deletedQuestions.add(question);

        // Update position for all posterior sibling questions
        for(BuilderQuestion q : questions){
                if (q.parent == question.parent && q.position > question.position)
                        q.position -= 1;
        }

        // Remove all questions for that parent, recursively
        String[] childrenIds = new String[]{};
        for(BuilderQuestion q : questions) if (q.parent == id) childrenIds.add(q.id);
        for(String childId : childrenIds) removeQuestion(childId);
    }

    list<BuilderQuestion>lSections = new list<BuilderQuestion>();
    list<BuilderQuestion>childQuest = new list<BuilderQuestion>();

    // Looks for the corresponding Question__c object.
    private Question__c getQuestion(BuilderQuestion bq, Boolean doCreateOrUpdate) {
                Question__c q = sfquestions.get(bq.id);
                if (doCreateOrUpdate) {
                        if (q != null) { bq.updateQuestion(q); }
                        else { q = bq.createQuestion(survey); sfquestions.put(bq.id, q); }
                }
                return q;
    }

    // Looks for the corresponding Option__c object.
    // If it doesn't exist, and a Question__c was provided, creates it.
    private Option__c getOption(BuilderQuestion.Option bo, Question__c question) {
                Option__c o = sfoptions.get(bo.id);
                if (question != null) {
                        if (o != null) { bo.updateOption(o); }
                        else { o = bo.createOption(question); sfoptions.put(bo.id, o); }
                }
                return o;
    }

    // Saves the list of questions to the database
    public void save(){
        this.error = '';
        Savepoint sp = Database.setSavepoint();
        // Validating that survey is not already published
        survey = [SELECT Id, Status__c,LastModifiedDate, Name FROM Survey__c WHERE id = :survey.id][0];
        if(survey.Status__c != 'Draft'){
            this.error = ERR_SURVEY_ALREADY_PUBLISHED;
            return;
        }else if(survey.LastModifiedDate != lastModified){
            //this is used to avoid errors in the JS since the Label may contain line jumps, so with this we indicate to JS the error, and we read the
            //lable from the VF page.
            this.error = 'BUILDER_ERR_CONCURRENTSAVE';
            return;
        }

        String pquestions = getParam('questions');
        update survey;
        //Refresh Last modified DATE.
        Survey__c currentSurvey = [SELECT LastModifiedDate FROM Survey__c WHERE Id=:this.survey.id limit 1];
        lastModified = currentSurvey.LastModifiedDate;
        if (pquestions != null){
            JSONParser parser = JSON.createParser(pquestions);
            BuilderQuestion[] newQuestions = (BuilderQuestion[])parser.readValueAs(BuilderQuestion[].class);

            // Delete all existing conditions
            delete [select Id from SkipCondition__c where Parent__r.Survey__c = :survey.Id];

            // Create all conditions again
            SkipCondition__c[] conditions = new SkipCondition__c[]{};
            for(BuilderQuestion bq : newQuestions){
                Question__c question = getQuestion(bq, false);
                if (question != null && bq.skipconditions != null){
                  for(BuilderQuestion.SkipCondition cond : bq.skipconditions)
                      conditions.add( cond.createSkipCondition(question) );
                }
            }
            insert conditions;
        }

        // # Upsert sections
        Question__c[] toUpdate = new Question__c[]{};
        Question__c[] toInsert = new Question__c[]{};
        for (BuilderQuestion bq : questions){
                if (bq.type == 'section'){
                    Question__c q = getQuestion(bq, true);
                    if(q.Id != null)
                        toUpdate.add(q);
                    else
                        toInsert.add(q);
                }
        }
        update toUpdate;
        insert toInsert;

        // # Upsert questions
        toUpdate.clear();
        toInsert.clear();
        for (BuilderQuestion bq : questions) {
                if (bq.type != 'section') {
                        Question__c q = getQuestion(bq, true);
                        if (bq.parent != null && bq.parent != '') {
                                BuilderQuestion bqparent = findQuestion(bq.parent);
                                Question__c parent = getQuestion(bqparent, false);
                                q.Parent__c = parent.Id;
                        }
                        if(q.Id != null)
                            toUpdate.add(q);
                        else
                            toInsert.add(q);
                }
        }
        update toUpdate;
        insert toInsert;

        // # Delete questions that were removed
        Question__c[] toDelete = new Question__c[]{};

        for (BuilderQuestion bq : deletedQuestions) {
                Question__c q = getQuestion(bq, false);
                if (q != null) toDelete.add(q);
        // Related option objects will be cascade-delete by master-detail relationship
        }
        try{
            delete toDelete;
        }catch(Exception es){
            Apexpages.addMessage(new ApexPages.Message(ApexPages.Severity.FATAL,es.getMessage()));
            Database.rollback(sp);
            return;
        }


        // 2. Convert BuilderQuestion.Option objects into actual Option__c objects
        Option__c[] opUpsert = new Option__c[]{};
        Option__c[] opDelete = new Option__c[]{};

        for (BuilderQuestion bq : questions) {
                Question__c q = getQuestion(bq, false);
            if (bq.options != null)
                for(BuilderQuestion.Option bo : bq.options)
                        opUpsert.add( getOption(bo, q) );

            if (bq.deletedOptions != null)
                for(String optionId : bq.deletedOptions) {
                    Option__c o = sfoptions.get(optionId);
                    if (o != null) opDelete.add(o);
                }
        }

        upsert opUpsert;
        delete opDelete;

        // 3. Clear the deletedQuestion list (these have been deleted already)
        for (BuilderQuestion q: deletedQuestions)
            sfquestions.remove(q.id);

        for (BuilderQuestion q: questions)
            if(q.deletedOptions != null){
                for (String optionId : q.deletedOptions)
                    sfoptions.remove(optionId);
                q.deletedOptions.clear();
            }
        deletedQuestions.clear();
    }

    //Move the selected question after to the destination selected question
    public void move(String originId, String destinationId){
        if(originId == destinationId && destinationId != null) return;
        BuilderQuestion movedQ = findQuestion(originId);
        BuilderQuestion destinationQ = findQuestion(destinationId);
        //Updating sections position
        if(movedQ.type == 'section'){
            //Update position for Section moved to first place
            if(destinationId == FIRST_SECTION){
                for(BuilderQuestion q: questions){
                    if(q.position < movedQ.position && q.type == 'section' )
                        q.position++;
                }
                movedQ.position = 1;
            }
            else{
                for(BuilderQuestion q: questions){
                    if(q.position > movedQ.position && q.type == 'section')
                        q.position--;
                }
                //Update moved section position
                movedQ.position = destinationQ.position +1;
                //Update position of sections following
                for(BuilderQuestion q: questions){
                    if(q.position >= movedQ.position && q.id != movedQ.id && q.type == 'section' )
                        q.position++;
                }
            }
        }
        else{
            // Update questions position
            for (BuilderQuestion q : questions){
                if (q.parent == movedQ.parent && q.position > movedQ.position)
                    q.position--;
            }
            // Update moved question position
            // (Consider if destination is section or question)
            if (destinationQ.type == 'section'){
                movedQ.parent = destinationQ.Id;
                movedQ.position = 1;
            } else {
                movedQ.parent = destinationQ.parent;
                movedQ.position = destinationQ.position + 1;
            }
            // Update position of questions following
            for (BuilderQuestion q : questions){
              if (q.parent == movedQ.parent && q.position >= movedQ.position && q.id != movedQ.id)
                q.position++;
            }
        }
    }
    // Shorthand method
    private String getParam(String key){ return ApexPages.CurrentPage().getParameters().get(key); }

    public PageReference addQuestionController(){
        try{
            String strQuestion= getParam('question');
            this.addQuestion(strQuestion);
            error='';
        }catch(Exception ex){
                error = ex.getMessage();
        }
        return null;
    }
    public PageReference editQuestionController(){
        try{
            String strQuestion= getParam('question');
            this.editQuestion(strQuestion);
            error='';
        }catch(Exception ex){
                error = ex.getMessage();
        }
        return null;
    }
    public PageReference moveQuestionController(){
        try{
            String originId = getParam('originId');
            String destinationId = getParam('destinationId');
            this.move(originId,destinationId);
            error='';
        }catch(Exception ex){
            error = ex.getMessage();
        }
        return null;
    }
    public PageReference removeQuestionController(){
        String id = getParam('qId');
        error= '';
        if(!hasRequiredMapped(id)){
            try{
                this.removeQuestion(id);
                error='';
            }catch(Exception ex){
                error = ex.getMessage();
            }
        }else{
            error = 'This question is mapped to a required field and cannot be removed. Please remove the mapping from the Field Mapping tab before deleting this question.';
        }
        return null;
    }
    public PageReference quickSave(){
        save();
        return null;
    }
    public PageReference saveContinue(){
        save();
        if(this.error.length() == 0){
            PageReference auxRef = Page.SurveyScoring;
            string surveyId = ApexPages.currentPage().getParameters().get('id');
            auxRef.getParameters().put('id', surveyId);
            return auxRef;
        }else{
            return null;
        }
    }
    private boolean hasRequiredMapped(string Id){
        QuestionMapping__c[] mapping = [SELECT Id, FieldApiName__c,SurveyMapping__r.ObjectApiName__c FROM QuestionMapping__c WHERE Question__c=:Id];
        for(QuestionMapping__c qM:mapping){
            Schema.DescribeFieldResult fieldDescribe = Schema.getGlobalDescribe().get(qM.SurveyMapping__r.ObjectApiName__c).getDescribe().fields.getMap().get(qM.FieldApiName__c).getDescribe();
            //If the Field is required and is not seted by it self then return false
            if(!fieldDescribe.isNillable() && !fieldDescribe.isDefaultedOnCreate())
                return true;
        }
        return false;
    }

}