/**
*  Grameen: Salesforce - PPI Upload
*  This is the test class for the LookupTablesController class
*  @author  Alejandro De Gregorio 
*/
@isTest
private class LookupTablesControllerTest {

    static final String FIRST_TABLE_NAME = '__Test__ First Table';
    static final String OLD_TABLE_NAME = '__Test__ Old Table';
    static final String VALID_TABLE_NAME = '__Test__ Valid Table';
    static final String INVALID_TABLE_NAME = '__Test__ Invalid Table';
    static final String MULTIPLE_TABLE_NAME = '__Test__ Multiple Table';
    static final String NON_UTF8_TABLE_NAME = '__Test__ Non UTF-8 File';
    static final String FILE_CONTENT_VALID = 'FROM,TO,' + VALID_TABLE_NAME + '\n' +
                                             '0,5,10\n' +
                                             '6,10,20\n' +
                                             '11,20,38';
    static final String FILE_CONTENT_VALID2 = 'FROM,TO,' + OLD_TABLE_NAME + '\n' +
                                             '0,5,24\n' +
                                             '6,10,36\n' +
                                             '11,20,49';
    static final String FILE_CONTENT_INVALID_RANGE = 'FROM,TO,' + INVALID_TABLE_NAME + '\n' +
                                                     '3,1,10\n' +
                                                     '6,10,20\n' +
                                                     '11,20,38';
    static final String FILE_CONTENT_OVERLAPPED_RANGE = 'FROM,TO,' + INVALID_TABLE_NAME + '\n' +
                                                        '0,5,10\n' +
                                                        '6,10,20\n' +
                                                        '8,20,38\n' +
                                                        '21,30,38';
    static final String FILE_CONTENT_WRONG_COLUMN_COUNT = 'FROM,TO,' + INVALID_TABLE_NAME + '\n' +
                                                          '0,5,10,24\n' +
                                                          '6,10,20\n' +
                                                          '11,20,38';
    static final String FILE_CONTENT_WRONG_COLUMN_COUNT2 = 'FROM,TO\n' +
                                                          '0,5,10,24\n' +
                                                          '6,10,20\n' +
                                                          '11,20,38';
    static final String FILE_CONTENT_NON_INTEGER = 'FROM,TO,' + INVALID_TABLE_NAME + '\n' +
                                                   '0,5,10\n' +
                                                   '6,test0,20\n' +
                                                   '11,20,38';
                                                   
    static final String FILE_CONTENT_MULTIPLE_TABLES = 'FROM,TO,' + MULTIPLE_TABLE_NAME + ' 1, ' + MULTIPLE_TABLE_NAME + ' 2, ' + MULTIPLE_TABLE_NAME + ' 3\n' +
                                                       '0,14,10,30,15\n' +
                                                       '15,30,25,46,20\n' +
                                                       '34,38,32,49,30\n' +
                                                       '39,48,48,87,57';
    static final Blob FILE_CONTENT_NON_UTF8 = Blob.toPDF('test non UTF-8');

    static testMethod void testPPIUpload() {
        LicenseSettings__c testLicense = newLicense(true,'Test');
        insert testLicense;
        
        //----------------------------------------------------------------
        // Initial data for testing
        //----------------------------------------------------------------
        ISO_Country__c country = new ISO_Country__c(Name = 'AA', ShortName__c = 'TEST');
        insert country;
        
        Survey__c survey = new Survey__c(IsPPI__c = true, CountryCode__c = 'AA', PPIDataYear__c = 2009);
        insert survey;
        
        PPITable__c firstTable = new PPITable__c(Name = FIRST_TABLE_NAME);
        insert firstTable;
        
        PPITable__c oldTable = new PPITable__c(Name = OLD_TABLE_NAME);
        insert oldTable;
        
        PPITableDataSet__c dataSet = new PPITableDataSet__c(Survey__c = survey.Id, PPITable__c = firstTable.Id);
        insert dataSet;
        
        List<PPITableLine__c> lines = new List<PPITableLine__c>();
        for(Integer i = 1; i < 10; i++){
            lines.add(new PPITableLine__c(PPITableDataSet__c = dataSet.Id, From__c = i*5, To__c = (i*5)+4, Probability__c = i*5));
        }
        insert lines;
        
        
        //----------------------------------------------------------------
        // Initialize controller
        //----------------------------------------------------------------
        ApexPages.currentPage().getParameters().put('id', survey.Id);
        LookupTablesController controller = new LookupTablesController();
        
        // Test that ppiTables was loaded
        System.assertEquals(1, controller.ppiTables.size());

        
        //----------------------------------------------------------------
        // Tests for the validateScoreRange method
        //----------------------------------------------------------------
        
        // Valid ranges should be saved
        controller.fileBinary = Blob.valueOf(FILE_CONTENT_VALID);
        controller.readFile();
        // The page shouldn't have error messages
        System.assertEquals(0, ApexPages.getMessages().size());
        PPITable__c savedTable = [SELECT Id, Name FROM PPITable__c WHERE Name = :VALID_TABLE_NAME];
        // There should be three lines saved for the table
        System.assertEquals(3, [SELECT Id FROM PPITableLine__c 
                                WHERE PPITableDataSet__r.PPITable__c = :savedTable.Id 
                                    AND PPITableDataSet__r.Survey__c = :survey.Id].size());
        
        // Invalid ranges should show an error
        controller.fileBinary = Blob.valueOf(FILE_CONTENT_INVALID_RANGE);
        controller.readFile();
        // The page should have an error messages
        System.assertEquals(1, ApexPages.getMessages().size());
        
        // Overlapped ranges should show an error
        controller.fileBinary = Blob.valueOf(FILE_CONTENT_OVERLAPPED_RANGE);
        controller.readFile();
        // The page should have an error messages
        System.assertEquals(2, ApexPages.getMessages().size());
        
        
        //----------------------------------------------------------------
        // Tests for the readFile method
        //----------------------------------------------------------------
        
        // Wrong column count should show an error
        controller.fileBinary = Blob.valueOf(FILE_CONTENT_WRONG_COLUMN_COUNT);
        controller.readFile();
        // The page should have an error messages
        System.assertEquals(3, ApexPages.getMessages().size());

        // Load a valid file after an error has occurred
        controller.fileBinary = Blob.valueOf(FILE_CONTENT_VALID);
        controller.readFile();
        // The tables and data sets from the failed upload shouldn't be created
        System.assertEquals(0, [SELECT Id FROM PPITable__c WHERE Name = :INVALID_TABLE_NAME].size());
        System.assertEquals(0, [SELECT Id FROM PPITableDataSet__c WHERE PPITable__r.Name = :INVALID_TABLE_NAME].size());
        
        // Wrong column count at the header should show an error
        controller.fileBinary = Blob.valueOf(FILE_CONTENT_WRONG_COLUMN_COUNT2);
        controller.readFile();
        // The page should have an error messages
        System.assertEquals(4, ApexPages.getMessages().size());
        
        // Non integer value should show an error
        controller.fileBinary = Blob.valueOf(FILE_CONTENT_NON_INTEGER);
        controller.readFile();
        // The page should have an error messages
        System.assertEquals(5, ApexPages.getMessages().size());
        
        // Uploading a non UTF-8 file should show an error
        controller.fileBinary = FILE_CONTENT_NON_UTF8;
        controller.readFile();
        // The page should have an error messages
        System.assertEquals(6, ApexPages.getMessages().size());
        // The table shouldn't be saved
        System.assertEquals(0, [SELECT Id, Name FROM PPITable__c WHERE Name = :NON_UTF8_TABLE_NAME].size());
        
        // Multiple tables should be saved
        controller.fileBinary = Blob.valueOf(FILE_CONTENT_MULTIPLE_TABLES);
        controller.readFile();
        System.assertEquals(3, [SELECT Id, Name FROM PPITable__c WHERE Name LIKE :MULTIPLE_TABLE_NAME + '%'].size());
        
        // Upload a table that already exists should replace the old lines
        Set<PPITableLine__c> oldLines = new Set<PPITableLine__c>();
        oldLines.addAll([SELECT Id FROM PPITableLine__c WHERE PPITableDataSet__r.PPITable__r.Name = :VALID_TABLE_NAME
                                                            AND PPITableDataSet__r.Survey__c = :survey.Id]);
        controller.fileBinary = Blob.valueOf(FILE_CONTENT_VALID);
        controller.readFile();
        List<PPITableLine__c> newLines = [SELECT Id FROM PPITableLine__c 
                                          WHERE PPITableDataSet__r.PPITable__r.Name = :VALID_TABLE_NAME
                                              AND PPITableDataSet__r.Survey__c = :survey.Id];
        System.assertEquals(false, oldLines.removeAll(newLines));
        
        // Upload data for an existent table should add a new data set and new lines
        controller.fileBinary = Blob.valueOf(FILE_CONTENT_VALID2);
        controller.readFile();
        List<PPITableDataSet__c> dataSetList = [SELECT Id FROM PPITableDataSet__c WHERE Survey__c = :survey.Id AND PPITable__c = :oldTable.Id];
        System.assertEquals(1, dataSetList.size());
        System.assertEquals(3, [SELECT Id FROM PPITableLine__c WHERE PPITableDataSet__c = :dataSetList[0].Id].size());
        
        
        //----------------------------------------------------------------
        // Tests for the deleteTable method
        //----------------------------------------------------------------
        
        // The table data set should be deleted
        controller.selectedTableId = firstTable.Id;
        controller.deleteDataSet();
        System.assertEquals(0, [SELECT Id FROM PPITableDataSet__c WHERE PPITable__c = :firstTable.Id].size());
    }
    static testMethod void checkLicense(){
        ISO_Country__c countryCode = new ISO_Country__c(Name='AA',ShortName__c='Test');
        insert countryCode;
        Survey__c survey = new Survey__c(IsPPI__c=true,CountryCode__c='AA',PPIDataYear__c = 2103);
        insert survey;
        LicenseSettings__c testLicense = newLicense(false,'Test');
        insert testLicense;
        try{
            ApexPages.currentPage().getParameters().put('id', survey.Id);
            LookupTablesController controller = new LookupTablesController();
        }catch(Exception e){
            system.assertEquals(system.label.SYSTEM_ERR_NOLICENSEPERMISSION, e.getMessage());   
        }
    }
    static LicenseSettings__c newLicense(Boolean PPIEditable, String licName){
        //Clean original license to do tests
        list<LicenseSettings__c> lS = [SELECT PPI_Editable__c FROM LicenseSettings__c limit 1];
        delete lS;
        LicenseSettings__c li = new LicenseSettings__c(PPI_Editable__c = PPIEditable, Name = licName);
        return li;
    }
    
}
