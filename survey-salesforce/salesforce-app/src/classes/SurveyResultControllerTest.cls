@isTest
private class SurveyResultControllerTest {

    static Question__c newQuestion(Survey__c survey, Question__c parent, String type, String name, String caption, Integer position){
        return new Question__c(Survey__c = survey.Id, Parent__c = (parent != null ? parent.Id : null), Type__c = type, Name = name, Caption__c = caption, Position__c = position);
    }

    static Option__c newOption(Question__c question, String name, Integer position){
        return new Option__c(Question__c = question.Id, Name = name, Caption__c = name, Position__c = position);
    }

    static Submission__c newSubmission(Survey__c survey, Date startdate, Contact surveyor, Contact interviewee){
        return new Submission__c(Survey__c = survey.Id, StartDate__c = startdate, Surveyor__c = surveyor.Id, Interviewee__c = interviewee.Id);
    }

    static Answer__c newTextAnswer(Submission__c sanswer, Question__c question, String value){
        return new Answer__c(Submission__c = sanswer.Id, Question__c = question.Id,
            Value__c = value, TextValue__c = value);
    }

    static Answer__c newOptionAnswer(Submission__c sanswer, Question__c question, Option__c option){
        return new Answer__c(Submission__c = sanswer.Id, Question__c = question.Id, Value__c = option.Caption__c,
            TextValue__c = option.Caption__c, Option__c = option.Id);
    }

    static Answer__c newDateAnswer(Submission__c sanswer, Question__c question, Date value){
        return new Answer__c(Submission__c = sanswer.Id, Question__c = question.Id,
            Value__c = String.valueOf(value), DateValue__c = value);
    }

    static Answer__c newDateTimeAnswer(Submission__c sanswer, Question__c question, DateTime value){
        return new Answer__c(Submission__c = sanswer.Id, Question__c = question.Id,
            Value__c = String.valueOf(value), DateTimeValue__c = value);
    }

    static Answer__c newNumberAnswer(Submission__c sanswer, Question__c question, Decimal value){
        return new Answer__c(Submission__c = sanswer.Id, Question__c = question.Id,
            Value__c = String.valueOf(value), NumericValue__c = value);
    }

    // Create a survey with questions of every kind.
    // Create a couple of survey answers.
    // Make sure all questions are answered in at least one survey, and unanswered in at least one survey.
    // Check that the columns and rows were created correctly.
    static testMethod void testSurveyParsing(){

        Contact alice   = new Contact(FirstName = 'Alice'  , LastName = 'Alice');
        Contact bob     = new Contact(FirstName = 'Bob'    , LastName = 'Bob');
        Contact charlie = new Contact(FirstName = 'Charlie', LastName = 'Charlie');
        insert new Contact[]{alice, bob, charlie};

        // Prepare survey and survey
        Survey__c survey = new Survey__c(); insert survey;
        Question__c sec1 = newQuestion(survey, null, 'section', 'section1', 'section1', 1);
        Question__c sec2 = newQuestion(survey, null, 'section', 'section2', 'section2', 2);
        insert new Question__c[]{sec1, sec2};

        // Prepare a question of each type
        Question__c stext = newQuestion(survey, sec1, 'text-short', 'text-short', 'text-short', 1);

        Question__c numbr = newQuestion(survey, sec1, 'number', 'number', 'number', 3);
        Question__c dateq = newQuestion(survey, sec2, 'date-date', 'date-date', 'date-date', 1);
        Question__c timeq = newQuestion(survey, sec2, 'date-datetime', 'date-datetime', 'date-datetime', 2);
        Question__c radio = newQuestion(survey, sec2, 'radio', 'radio', 'radio', 3);
        Question__c chkbx = newQuestion(survey, sec2, 'checkbox', 'checkbox', 'checkbox', 4);
        insert new Question__c[]{stext, numbr, dateq, timeq, radio, chkbx};

        Option__c radio1 = newOption(radio, 'radio-1', 1);
        Option__c radio2 = newOption(radio, 'radio-2', 2);
        Option__c radio3 = newOption(radio, 'radio-3', 3);
        Option__c chkbx1 = newOption(chkbx, 'chkbx-1', 1);
        Option__c chkbx2 = newOption(chkbx, 'chkbx-2', 2);
        Option__c chkbx3 = newOption(chkbx, 'chkbx-3', 3);
        insert new Option__c[]{radio1, radio2, radio3, chkbx1, chkbx2, chkbx3};

        // Insert a couple of submissions
        Submission__c sans1 = newSubmission(survey, Date.valueOf('2011-01-01'), alice, bob);
        Submission__c sans2 = newSubmission(survey, Date.valueOf('2011-01-02'), alice, charlie);
        Submission__c sans3 = newSubmission(survey, Date.valueOf('2011-01-03'), alice, charlie); // will be left empty
        insert new Submission__c[]{sans1, sans2, sans3};

        Answer__c[] sans1Ans = new Answer__c[]{
            newTextAnswer(sans1, stext, 'stext'),
            newNumberAnswer(sans1, numbr, 30)
        };
        Answer__c[] sans2Ans = new Answer__c[]{
            newTextAnswer(sans2, stext, 'stext'),
            newDateAnswer(sans2, dateq, Date.valueOf('2011-01-01')),
            newDateTimeAnswer(sans2, timeq, DateTime.valueOf('2011-01-01 10:00:00')),
            newOptionAnswer(sans2, chkbx, chkbx1),
            newOptionAnswer(sans2, chkbx, chkbx2)
        };
           insert sans1Ans;
           insert sans2Ans;
        ScoringGroup__c[] sGs = new  ScoringGroup__c[]{
            new ScoringGroup__c(Survey__c = survey.Id,Caption__c = 'ScoreGroup1'),
            new ScoringGroup__c(Survey__c = survey.Id,Caption__c = 'ScoreGroup2'),
            new ScoringGroup__c(Survey__c = survey.Id,Caption__c = 'ScoreGroup3')
        };
        insert  sGs;
        insert new ScoreValue__c[]{
            new ScoreValue__c(ScoringGroup__c = sGs[0].Id, Value__c=100,Option__c = radio1.id),
            new ScoreValue__c(ScoringGroup__c = sGs[1].Id, Value__c=1000,Option__c = radio2.id)
        };
        Answer__c qAns = newOptionAnswer(sans1, radio, radio1);
        insert qAns;
        insert new SubmissionScore__c(ScoringGroup__c = sGs[0].Id,Submission__c = sans1.id,TotalScore__c = 100);
        test.startTest();

        ApexPages.currentPage().getParameters().put( 'id', survey.Id );
        SurveyResultController controller = new SurveyResultController();

        test.stopTest();
        system.assertEquals( 4 + 6 + sGs.size(), controller.columnHeaders.size()); // 4 fixed columns, 6 questions, 1 ScoreGroups

        SurveyResultController.SubmissionRow[] rows = controller.submissionRows;
        system.assertEquals( 3, rows.size() );

        system.assertEquals( 4 + 6 + sGs.size(), rows[0].cells.size() );
        system.assertEquals( Date.valueOf('2011-01-01'), rows[0].cells[1].datetimeValue );
        system.assertEquals( 'Alice Alice', rows[0].cells[2].stringValue );
        system.assertEquals( 'Bob Bob', rows[0].cells[3].stringValue );
        system.assertEquals( 'stext', rows[0].cells[4].stringValue );
        system.assertEquals( 30, rows[0].cells[5].numericValue );
        system.assertEquals( null, rows[0].cells[6].dateValue );        system.assertEquals( '', rows[0].cells[6].stringValue );
        system.assertEquals( null, rows[0].cells[7].dateTimeValue );   system.assertEquals( '', rows[0].cells[7].stringValue );
        system.assertEquals( 'radio-1', rows[0].cells[8].stringValue );
        system.assertEquals( '', rows[0].cells[9].stringValue );
        system.assertEquals( 100, rows[0].cells[10].numericValue );
        system.assertEquals( null, rows[0].cells[11].numericValue );
        system.assertEquals( 4 + 6 + sGs.size(), rows[1].cells.size() );
        system.assertEquals( Date.valueOf('2011-01-02'), rows[1].cells[1].datetimeValue );
        system.assertEquals( 'Alice Alice', rows[1].cells[2].stringValue );
        system.assertEquals( 'Charlie Charlie', rows[1].cells[3].stringValue );
        system.assertEquals( 'stext', rows[1].cells[4].stringValue );
        system.assertEquals( '', rows[1].cells[5].stringValue );
        system.assertEquals( Date.valueOf('2011-01-01'), rows[1].cells[6].dateValue );
        system.assertEquals( DateTime.valueOf('2011-01-01 10:00:00'), rows[1].cells[7].dateTimeValue );
        system.assertEquals( '', rows[1].cells[8].stringValue );
        system.assertEquals( 'chkbx-1, chkbx-2', rows[1].cells[9].stringValue );

        system.assertEquals( 4 + 6 + sGs.size(), rows[1].cells.size() );
        system.assertEquals( Date.valueOf('2011-01-03'), rows[2].cells[1].datetimeValue );
        system.assertEquals( 'Alice Alice', rows[2].cells[2].stringValue );
        system.assertEquals( 'Charlie Charlie', rows[2].cells[3].stringValue );
    }
}