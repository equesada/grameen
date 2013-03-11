public with sharing class DBFullAccessTest {
    static testMethod void TestDMLOperations(){
        Profile standardProfile = [SELECT Id FROM Profile WHERE Name = 'Standard User' limit 1];
        User standardUser =  new User(
            UserName = 'test-user@test-company.com',
            FirstName = 'Test-First-Name',
            LastName = 'Test-Last-Name',
            Alias = 'test',
            Email = 'test-user@test-company.com',
            EmailEncodingKey = 'UTF-8',
            LanguageLocaleKey = 'en_US',
            LocalesIdKey = 'en_US',
            TimezonesIdKey = 'America/Los_Angeles',
            ProfileId = standardProfile.Id
            );
        insert standardUser;
        //Contact without account shall be private by default.
        Contact privateContact = new Contact(Lastname ='Doe');
        insert privateContact;
        system.runAs(standardUser){
            //Test Query
            List<Contact> contacts = (List<Contact>)DBFullAccess.QuerySObject('SELECT Id FROM Contact WHERE Id=\''+ privateContact.Id+'\' limit 1');
            //I should be able to query the contact even though the contact is private.
            system.assertEquals(1,contacts.size());

            //Test UPDATE
            privateContact.LastName = 'Doe2';
            DBFullAccess.UpdateSObject(privateContact);
            contacts = (List<Contact>)DBFullAccess.QuerySObject('SELECT Id,LastName FROM Contact WHERE Id=\''+ privateContact.Id+'\' limit 1');
            //The contact should be updated.
            system.assertEquals('Doe2',contacts[0].LastName);

            //Test INSERT
            Contact testContact = new Contact(Lastname ='smith');
            DBFullAccess.InsertSObject(testContact);
            //The contact should be inserted.
            system.assertNotEquals(null,testContact.id);

            //Test DELETE
            DBFullAccess.DeleteSObject(testContact);
            contacts = (List<Contact>)DBFullAccess.QuerySObject('SELECT Id,LastName FROM Contact WHERE Id=\''+ testContact.Id+'\' limit 1');
            //The contact should be deleted.
            system.assertEquals(0,contacts.size());

            //Test UPSERT
            testContact = new Contact(Lastname ='smitha');
            DBFullAccess.UpsertSObject(testContact);
            contacts = (List<Contact>)DBFullAccess.QuerySObject('SELECT Id,LastName FROM Contact WHERE Id=\''+ testContact.Id+'\' limit 1');
            //The contact should be inserted again.
            system.assertEquals(1,contacts.size());
            testContact.Birthdate = Date.today();
            DBFullAccess.UpsertSObject(testContact);
            contacts = (List<Contact>)DBFullAccess.QuerySObject('SELECT Id,LastName,BirthDate FROM Contact WHERE Id=\''+ testContact.Id+'\' limit 1');
            system.assertEquals(testContact.Birthdate,contacts[0].Birthdate);

            //With <LIST> AS PARAMETER.

            //Test UPDATE
            privateContact.LastName = 'Doe3';
            testContact.LastName = 'Smith123';
            DBFullAccess.UpdateSObject(new List<Contact>{privateContact,testContact});
            contacts = (List<Contact>)DBFullAccess.QuerySObject('SELECT Id,LastName FROM Contact WHERE Id=\''+ privateContact.Id+'\' or Id=\''+ testContact.id +'\' limit 2');
            //The contact should be updated.
            system.assertEquals('Doe3',contacts[0].LastName);
            system.assertEquals('Smith123',contacts[1].LastName);

            //Test INSERT
            Contact testContact2 = new Contact(Lastname ='smith3');
            Contact testContact3 = new Contact(Lastname ='smith4');
            DBFullAccess.InsertSObject(new List<Contact>{testContact2,testContact3});
            //The contact should be inserted.
            system.assertNotEquals(null,testContact2.id);
            system.assertNotEquals(null,testContact3.id);

            //Test DELETE
            DBFullAccess.DeleteSObject(new List<Contact>{testContact2,testContact3});
            contacts = (List<Contact>)DBFullAccess.QuerySObject('SELECT Id,LastName FROM Contact WHERE Id=\''+ testContact2.Id+'\' or Id=\''+ testContact3.id +'\'');
            //The contact should be deleted.
            system.assertEquals(0,contacts.size());

            //Test UPSERT
            Contact testContact4 = new Contact(Lastname ='smith4');
            Contact testContact5 = new Contact(Lastname ='smith5');
            DBFullAccess.UpsertSObject(new List<Contact>{testContact4,testContact5});
            contacts = (List<Contact>)DBFullAccess.QuerySObject('SELECT Id,LastName FROM Contact WHERE Id=\''+ testContact4.Id+'\' or Id=\''+ testContact5.id +'\'');
            //The contact should be inserted again.
            system.assertEquals(2,contacts.size());
            testContact4.Birthdate = Date.today();
            testContact5.Birthdate = Date.today();
            DBFullAccess.UpsertSObject(new List<Contact>{testContact4,testContact5});
            contacts = (List<Contact>)DBFullAccess.QuerySObject('SELECT Id,LastName,BirthDate FROM Contact WHERE Id=\''+ testContact4.Id+'\' or Id=\''+ testContact5.id +'\'');
            system.assertEquals(testContact4.Birthdate,contacts[0].Birthdate);
            system.assertEquals(testContact5.Birthdate,contacts[1].Birthdate);
        }
    }
}