/**
*   This trigger set the name of the survey if it is a PPI Survey
*   @author Alejandro De Gregorio
*/
trigger SetPPISurveyName on Survey__c (before insert, before update, after update) {
    Survey__c currentSurvey = null;
    Question__c prevQuestion = null;
    
    // Get all questions from the edited survey.
    // Skip questions that do not belong to the survey's current draft version (since those can't be edited)
    List <Question__c> quests = [
        SELECT Id, Name, Parent__c, Survey__c, Position__c, Type__c, FromVersion__c, ToVersion__c
        FROM Question__c 
        WHERE Survey__c IN :trigger.new AND IsDraft__c = 1
        ORDER BY Survey__c, Parent__c, Position__c];

    Map<String, Survey__c> surveysByName = new Map<String, Survey__c>();
    //Messages
    public final String ERR_SURVEY_PPI_UNIQUE = 'There is already a PPI Survey for this Country and Year. Please select another country or year.';
    public final String ERR_INVALID_COUNTRY_CODE = 'Invalid country code: {0}';

    for(Survey__c survey:[SELECT Name FROM Survey__c WHERE IsPPI__c = true])
       surveysByName.put(survey.Name, survey);
    
    if(trigger.isAfter && trigger.isUpdate){
	    for(Question__c q : quests){
            if (currentSurvey == null || currentSurvey.Id != q.Survey__c){
                currentSurvey = trigger.newMap.get(q.Survey__c);
            }
            // Raise an error if the question shares position with the previous question
            if(prevQuestion != null
                    && q.Survey__c == prevQuestion.Survey__c 
                    && q.Parent__c == prevQuestion.Parent__c 
                    && q.Position__c == prevQuestion.Position__c){
                
                currentSurvey.addError('Question ' + q.Name +' Id = '+ q.Id + ' and Quesiton ' + prevQuestion.Name + ' Id = ' + prevQuestion.Id + ' Share parent Id= ' + q.Parent__c + ' and Position ' + q.Position__c );   
            }
            prevQuestion = q;
	     }
    }else{
	    for(Survey__c newSurvey : trigger.new){
	        // Set the survey name if it is a PPI Survey
	        if(newSurvey.IsPPI__c == true){
	            ISO_Country__c country = ISO_Country__c.getInstance(newSurvey.CountryCode__c);
	            if (country == null){
	                newSurvey.CountryCode__c.addError(String.format(ERR_INVALID_COUNTRY_CODE, new String[]{newSurvey.CountryCode__c}));
	            } else {
	                newSurvey.Name = country.ShortName__c + ' PPI ' + newSurvey.PPIDataYear__c;
	            }
	        }
	        if(surveysByName.containsKey(newSurvey.Name)){
	            if(trigger.isInsert || surveysByName.get(newSurvey.Name).Id != newSurvey.Id)
	              newSurvey.addError(ERR_SURVEY_PPI_UNIQUE);
	        }
	    }
    }
}