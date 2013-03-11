public with sharing class SurveyResultController {

    public Id actualSurveyId {get; set;} // Id of the selected Survey
    public String actualSurveyName {get; set;} // Name of the selected Survey
    public List<SelectOption> surveyItems {get;set;} // List of SelectOption of the Survey with at least one Answer
    public List<ColumnHeader> columnHeaders {get;set;} // List of Question Data to be shown in the header of the table
    public List<SubmissionRow> submissionRows {get;set;} //Data to be shown in the table
    public String stringIdSurveys {get;set;} // Comma separated String with the values of the selected Answers Id (in order to generate CSV)

    private static final Integer CUSTOM_LIMIT = 1000;
    /**
     *  Class constructor.
     */
    public SurveyResultController(){
        initData();
        if(ApexPages.currentPage().getParameters().get('id') != null){
            actualSurveyId = ApexPages.currentPage().getParameters().get('id');
            setSurveyName(actualSurveyId);
            reloadSurvey();
        }
    }

    public PageReference changeSurvey(){
        String surveyId = ApexPages.currentPage().getParameters().get('surveyId');
        PageReference pr = Page.SurveyResult;
        pr.getParameters().put('id',surveyId);
        pr.setRedirect(true);
        return pr;
    }

    /**
     *  Creates the SelectOption of the list of surveys with answers.
     */
    public void getSurveysOption(){
        this.surveyItems = new List<SelectOption>();
        for (Survey__c s : [SELECT Id, Name
                            FROM Survey__c
                            WHERE ResponseCount__c > 0
                            ORDER BY Name ]){
            this.surveyItems.add( new SelectOption(s.Id, s.Name) );
        }
    }

     /**
     *  Rerender header and data of the table.
     */
    public void reloadSurvey(){
        setSurveyName(this.actualSurveyId);
        createColumnHeaders(this.actualSurveyId);  // load the list of columns
        createTableRows(this.actualSurveyId); // load each survey answer, and it's set of question answers
    }

    /**
     *  Rerender header and data of the table.
     */
    public void setSurveyName(Id surveyId){
        Survey__c survey = [SELECT Id, Name, Alias__c FROM Survey__c WHERE Id = :surveyId ][0];
        actualSurveyName = (survey.Alias__c == null || survey.Alias__c == '') ? survey.Name : survey.Alias__c;
    }

    /**
     *  Creates the header to show in the table.
     */
    private void createColumnHeaders(Id surveyId){

        if(surveyId != null) {

            // Creates the fixed columns of the table...
            columnHeaders = new ColumnHeader[]{
                new ColumnHeader(null, null, 'No', QuestionType.QUESTION_NUMBER,null ),
                new ColumnHeader(null, null, 'Submitted', QuestionType.QUESTION_DATE, null ),
                new ColumnHeader(null, null, 'Surveyor', QuestionType.QUESTION_TEXT_SHORT,null ),
                new ColumnHeader(null, null, 'Respondent', QuestionType.QUESTION_TEXT_SHORT,null )
            };

            // Get all non-section questions for this survey
            Question__c[] surveyQuestions = [SELECT Id, Caption__c, Name, Type__c
                                             FROM Question__c
                                             WHERE Type__c != :QuestionType.QUESTION_SECTION
                                                 and Type__c != :QuestionType.QUESTION_STATIC and Survey__c = :surveyId
                                             ORDER BY Parent__r.Position__c, Position__c
                                             LIMIT :CUSTOM_LIMIT ];

            // Get a map for all the options. It will be used to convert ids into strings.
            // Also get a map from question to that question's list of options
            Map<Id,Option__c[]> questionOptions = new Map<Id,Option__c[]>();
            for (Option__c o : [SELECT Id, Name, Caption__c, Question__c
                                FROM Option__c
                                WHERE Question__r.Survey__c= :surveyId
                                ORDER BY Question__c, Position__c]){
                Option__c[] options = questionOptions.get(o.Question__c);
                if (options == null) questionOptions.put(o.Question__c, new Option__c[]{o});
                else options.add(o);
            }

            // ... now creates the variable columns of the table.
            for(Question__c question : surveyQuestions){

                columnHeaders.add( new ColumnHeader(question.Id, null, question.Caption__c, question.Type__c,null) ); // Simple question
            }
            //Scoring Groups Columns
            List<ScoringGroup__c> scoreGs = [SELECT Id,Caption__c,Survey__c FROM ScoringGroup__c WHERE ScoringGroup__c.Survey__c =:surveyId];
            for(ScoringGroup__c sG:scoreGs){
                columnHeaders.add(new ColumnHeader(null, null, sG.Caption__c, QuestionType.QUESTION_NUMBER,sG.Id));
            }
        }
    }


    /**
     *  Creates the data content of the table.
     */
    private void createTableRows(Id surveyId){

        if(surveyId != null){
            submissionRows = new SubmissionRow[]{};

            //Retrieves Answer data...
            map<String, map<String, String>> mapOfSurveyAns = new map<String, map<String, String>>(); // Map of data values of the table

            Submission__c[] submissions = [SELECT Id, Name, Status__c, Interviewee__c, Surveyor__c, StartDate__c,gps_x__c ,gps_y__c
                                               FROM Submission__c
                                               WHERE Survey__c = :surveyId
                                               ORDER BY StartDate__c
                                               LIMIT :CUSTOM_LIMIT ];
            List<SubmissionScore__c> answerScores = [SELECT Id,TotalScore__c,Submission__c,ScoringGroup__c FROM SubmissionScore__c WHERE Submission__c in :submissions];
            Map<Id,Map<Id,SubmissionScore__c>> answerIdToScore = new Map<Id,Map<Id,SubmissionScore__c>>();
            for(Submission__c sAns:submissions){
                for(SubmissionScore__c sVal:answerScores){
                    if(sVal.Submission__c == sAns.Id){
                        Map<Id,SubmissionScore__c> survLst = answerIdToScore.get(sAns.Id);
                        if(survLst == null)
                            answerIdToScore.put(sAns.Id,new Map<Id,SubmissionScore__c>{sVal.ScoringGroup__c => sVal});
                         else
                            survLst.put(sVal.ScoringGroup__c,sVal);
                    }
                }
            }
            if(submissions != null && !submissions.isEmpty()){

                // Get the names of interviewees and surveyors
                Set<Id> contactIds = new Set<Id>();
                for (Submission__c su : submissions) {
                    contactIds.add(su.Surveyor__c);
                    contactIds.add(su.Interviewee__c);
                }
                Map<Id,String> contactNames = new Map<Id,String>();
                for (Contact c : [Select Id, Name from Contact where Id in :contactIds])
                    contactNames.put(c.Id, c.Name);

                // Get all question answers for this survey
                Answer__c[] answersList = [SELECT Id, Question__r.Id, Question__r.Type__c, Value__c, DateTimeValue__c, DateValue__c,NumericValue__c,TextValue__c , Option__r.Name, Question__c, Submission__r.Id
                                                           FROM Answer__c
                                                           WHERE Submission__r.Survey__c = :surveyId
                                                           ORDER BY Submission__c, Question__r.Parent__r.Position__c, Question__r.Position__c, Option__r.Position__c
                                                           LIMIT :CUSTOM_LIMIT ];

                // Group answers by submission and question
                // SurveyId -> ( QuestionId -> Answer__c[] )
                Map<Id, Map<Id, Answer__c[]>> submissionQuestions = new Map<Id, Map<Id, Answer__c[]>>();
                for(Answer__c qa : answersList){
                    Map<Id, Answer__c[]> maps = submissionQuestions.get(qa.Submission__c);
                    if (maps == null){
                        maps = new Map<Id, Answer__c[]>();
                        submissionQuestions.put(qa.Submission__c, maps);
                    }
                    Answer__c[] l = maps.get(qa.Question__c);
                    if (l == null) maps.put(qa.Question__c, new Answer__c[]{qa} );
                    else l.add(qa);
                }


                // For each column, look for this row's answer for that question and
                // add that cell to the row

                Integer rowNumber = 1;
                for(Submission__c su : submissions){

                    // 1. Create SubmissionRow
                    SubmissionRow row = new SubmissionRow(su.Id);
                    submissionRows.add(row);

                    // 2. Fill fixed cells
                    row.addCell( String.valueOf(rowNumber++) );
                    row.addCell( su.StartDate__c );
                    row.addCell( contactNames.get(su.Surveyor__c) );
                    row.addCell( contactNames.get(su.Interviewee__c) );
                    // 2.1 if submission is geotagged, provide a link to it's location on Google Maps
                    if (su.GPS_x__c != null && su.GPS_y__c != null){
                        row.cells[1].extraContent = '&nbsp;<a title="View Location" target="_blank" href="http://maps.google.com/maps?q=' +
                            su.GPS_y__c + ',' + su.GPS_x__c + '"><img alt="View Location" src="http://maps.gstatic.com/favicon.ico"></a>';
                    }

                    // 3. Create each SubmissionCell
                    Map<Id,Answer__c[]> answers = submissionQuestions.get(su.Id);
                    for(ColumnHeader header : columnHeaders){
                        if (header.questionID == null){
                            if(header.scoreGroupId != null){
                                //Try to get the score Value, if there is no score for this just fill the cell with a blank space.
                                try {
                                    row.addCell(integer.valueof(answerIdToScore.get(su.Id).get(header.scoreGroupId).TotalScore__c));
                                }catch(Exception e){
                                    row.addCell(' ');
                                }
                            }
                            continue;
                        }

                        // Look for the answer for this column in this Submission (get from map)
                        // Search for the answers of this question
                        Answer__c[] qas = answers != null ? answers.get(header.questionId) : null;
                        if (qas != null && !qas.isEmpty()) {

                            // Depending on the question type, add the corresponding cell
                            String type = header.type;

                            if (type == QuestionType.QUESTION_DATE) {
                                Date val = qas[0].DateValue__c;
                                if (val != null) row.addCell( val );
                                else row.addCell('');

                            } else if (type == QuestionType.QUESTION_DATETIME) {
                                DateTime val = qas[0].DateTimeValue__c;
                                if (val != null) row.addCell( dateTime.valueOf(val) );
                                else row.addCell('');

                            } else if (type == QuestionType.QUESTION_NUMBER) {
                                Decimal val = qas[0].NumericValue__c;
                                if (val != null) row.addCell(val);
                                else row.addCell('');

                            } else if (type == QuestionType.QUESTION_CHECKBOX) {
                                String str = '';
                                for (Answer__c q : qas) str += (str == '' ? '' : ', ') + q.TextValue__c ;
                                    row.addCell(str);

                            } else {
                                row.addCell( qas.isEmpty() ? '' : qas[0].Value__c );
                            }
                        }else row.addCell('');
                    }
                }
            }
        }
    }

    /**
    *  Instantiate data.
    */
    public void initData(){
        surveyItems = new List<SelectOption>();
        columnHeaders  = new List<ColumnHeader>();
        submissionRows = new List<SubmissionRow>();
        getSurveysOption();
    }

    //-------------------Inner classes----------------------


    /**
     *   This class have the caption and id of the question (Not necessary but there is too many maps and list already)
     *   Done in order to improve readability.
     *
     */
    public class ColumnHeader {
        public Id questionId {get;set;}
        public Id optionId {get;set;}
        public String caption {get;set;}
        public String type {get;set;}
        public Id scoreGroupId{get;set;}
        public ColumnHeader(Id questionId, Id optionId, String caption, String type,String scoreGroupId){
            this.questionId = questionId;
            this.optionId   = optionId;
            this.caption    = caption;
            this.type       = type;
            this.scoreGroupId = scoreGroupId;
        }
    }

    /**
     *   This class represents each row inside the table.
     *   Wrapper class to use nested apex:repeat in visualforce
     */
    public class SubmissionRow {
        public Id submissionId {get;set;}
        public SubmissionCell[] cells {get;set;}

        public SubmissionRow(Id answerId){
            this.submissionId = answerId;
            cells = new SubmissionCell[]{};
        }

        public void addCell(String   value){ cells.add(new SubmissionCell(value)); }
        public void addCell(Date     value){ cells.add(new SubmissionCell(value)); }
        public void addCell(Datetime value){ cells.add(new SubmissionCell(value)); }
        public void addCell(Decimal  value){ cells.add(new SubmissionCell(value)); }
    }

    /**
     *   This class have the caption of the question (Not necessary but there is too many maps and list already)
     *   Done in order to improve readability.
     *   Similar to ColumnHeader.
     *   Stores the received value in different properties to be able to reference it in a more specific way in the page
     */
    public static final String DATATYPE_STRING   = 'STRING';
    public static final String DATATYPE_DATE     = 'DATE';
    public static final String DATATYPE_DATETIME = 'DATETIME';
    public static final String DATATYPE_NUMERIC  = 'NUMERIC';

    public class SubmissionCell{
        public String stringValue {get;set;}
        public Date dateValue {get;set;}
        public DateTime datetimeValue {get;set;}
        public Decimal numericValue{get;set;}
        public String valueType {get;set;}
        public String extraContent {get;set;} // Store any HTML that should be displayed after the value

        public SubmissionCell(String   value){ stringValue   = value; valueType = DATATYPE_STRING;  }
        public SubmissionCell(Date     value){ dateValue     = value; valueType = DATATYPE_DATE;    }
        public SubmissionCell(Datetime value){ datetimeValue = value; valueType = DATATYPE_DATETIME;}
        public SubmissionCell(Decimal  value){ numericValue  = value; valueType = DATATYPE_NUMERIC; }

    }
}