trigger QuestionMappingCheckValidFieldType on QuestionMapping__c (before insert, before update) {

    public final String ERR_FIELD_ALREADY_MAPPED = 'A field is being referenced more than once for this surveyMapping';
    public final String ERR_FIELD_NO_MATCH_QUESTION = 'A field type doesn\'t match with the question type of the field';

    /*Inizializate a Map with the QuestionType and all valid Fields Type for each Question Type*/

    static final Map<String,Set<String>> validMappings = new Map<String,Set<String>>{
        QuestionType.QUESTION_TEXT_SHORT => new Set<String>{'String','TextArea'},
        QuestionType.QUESTION_TEXT_LONG => new Set<String>{'TextArea'},
        QuestionType.QUESTION_NUMBER => new Set<String>{
            'String','TextArea','Percent','Integer','Currency','Double'},
        QuestionType.QUESTION_NUMBER_INTEGER => new Set<String>{
            'String','TextArea','Percent','Integer','Currency','Double'},
        QuestionType.QUESTION_NUMBER_DECIMAL => new Set<String>{
            'String','TextArea','Percent','Integer','Currency','Double'},
        QuestionType.QUESTION_DATE=> new Set<String>
            {'String','TextArea','Date','DateTime'},
        QuestionType.QUESTION_DATETIME=> new Set<String>
            {'String','TextArea','DateTime'},
        QuestionType.QUESTION_RADIO=> new Set<String>
            {'String','TextArea','Picklist'},
        QuestionType.QUESTION_CHECKBOX=> new Set<String>
            {'String','TextArea','MultiPickList'}
    };

    Set<Id> surveyMappingsIds = new Set<Id>();
    Set<Id> questionsIds = new Set<Id>();
    //Map whit FieldApiNames to map,each with a list of SurveyMappingIds that map this field.
    Map <String, Set<Id> > fieldWithSurveyMappingList = new Map<String, Set<Id> >();
    Set<Id> fieldsToMap = new Set<Id>();
    Map<Id,Question__c> idToQuestion = new Map<Id,Question__c>();
    Map<Id,SurveyMapping__c> idToSurveyMapping = new Map<Id,SurveyMapping__c>();
    for (QuestionMapping__c questionMapping:trigger.new){
        questionsIds.add(questionMapping.Question__c);
        surveyMappingsIds.add(questionMapping.SurveyMapping__c);
    }
    List<SurveyMapping__c> surveyMappingList = [select Name,ObjectApiName__c,MatchingField__c,Survey__c from SurveyMapping__c where Id in :SurveyMappingsIds];

    for(SurveyMapping__c surveyMapping:surveyMappingList){
        idToSurveyMapping.put(surveyMapping.Id,surveyMapping);
    }
    List<Question__c> questions= [select Type__c,Name from Question__c where Id in :QuestionsIds];
    for(Question__c question:questions){
        idToQuestion.put(question.Id,question);
    }
    List<QuestionMapping__c> existingQuestionMappings = [select SurveyMapping__c,FieldApiName__c from QuestionMapping__c where SurveyMapping__c in :surveyMappingList ];

    //loop throught thq questionMapping in the DB to fill the a map of mappedFields and a list of the SurveyMapping
    //in which this Fields are being mapped
    for(QuestionMapping__c questionMapping:existingQuestionMappings){
        if (fieldWithSurveyMappingList.get(questionMapping.FieldApiName__c) == null)
            fieldWithSurveyMappingList.put(questionMapping.FieldApiName__c,new Set<Id>{questionMapping.SurveyMapping__c});
        else{
            fieldWithSurveyMappingList.get(questionMapping.FieldApiName__c).add(questionMapping.SurveyMapping__c);
        }
    }
    for (QuestionMapping__c questionMapping:trigger.new){
        boolean validFieldQuestionTypes;
        SObject mappedObject;
        //check if the Field is alredy mapped by the same surveyMapping__c and add it to the list
        if (fieldWithSurveyMappingList.get(questionMapping.FieldApiName__c)==null){
            fieldWithSurveyMappingList.put(questionMapping.FieldApiName__c,new Set<Id>{questionMapping.SurveyMapping__c});
        }else if(fieldWithSurveyMappingList.get(questionMapping.FieldApiName__c).contains(questionMapping.SurveyMapping__c)){
            questionMapping.FieldApiName__c.AddError(ERR_FIELD_ALREADY_MAPPED);
        }else{
            fieldWithSurveyMappingList.get(questionMapping.FieldApiName__c).add(questionMapping.SurveyMapping__c);
        }
        Schema.DescribeSObjectResult mappedObjectDescribe = Schema.getGlobalDescribe().get(idToSurveyMapping.get(questionMapping.SurveyMapping__c).ObjectApiName__c).getDescribe();
        Map<String, Schema.SObjectField> FieldDescribeMap = mappedObjectDescribe.fields.getMap();

        String fieldDisplayType= displayTypeToString(FieldDescribeMap.get(questionMapping.FieldApiName__c).getDescribe().getType());
        String sourceQuestionType=idToQuestion.get(questionMapping.Question__c).Type__c;

        //Checking field and question type compatibility
        if(!validMappings.get(sourceQuestionType).contains(fieldDisplayType)){
                questionMapping.FieldApiName__c.AddError(ERR_FIELD_NO_MATCH_QUESTION);
        }
    }


    /*function that relates a Schema.DisplayType with a String*/


    private static String displayTypeToString(Schema.DisplayType schemaType){
        String stringType;

        if (schemaType == Schema.DisplayType.String){
            stringType = 'String';
        }else if(schemaType == Schema.DisplayType.TextArea){
            stringType = 'TextArea';
        }else if(schemaType == Schema.DisplayType.Percent){
            stringType = 'Percent';
        }else if(schemaType == Schema.DisplayType.Integer){
            stringType = 'Integer';
        }else if(schemaType == Schema.DisplayType.Currency){
            stringType = 'Currency';
        }else if(schemaType == Schema.DisplayType.Double){
            stringType = 'Double';
        }else if(schemaType == Schema.DisplayType.Date){
            stringType = 'Date';
        }else if(schemaType == Schema.DisplayType.DateTime){
            stringType = 'DateTime';
        }else if(schemaType == Schema.DisplayType.Picklist){
            stringType = 'Picklist';
        }else if(schemaType == Schema.DisplayType.MultiPickList){
            stringType = 'MultiPickList';
        }
    return stringType;
    }
}