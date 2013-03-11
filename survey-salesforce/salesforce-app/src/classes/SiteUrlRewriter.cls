global class SiteUrlRewriter implements Site.UrlRewriter {

    global PageReference mapRequestUrl(PageReference friendlyUrl){
        String url = friendlyUrl.getUrl();
        if (url.startsWith('/apex/')){
            url = url.substring(5);
        }

        if (url.toLowerCase().startsWith('/formlist')) {
            return Page.formList;
        } else if (url.toLowerCase().startsWith('/submission')) {
            return Page.Submission;
        } else {
            return friendlyUrl;
        }
    }
    
    global PageReference[] generateUrlFor(PageReference[] yourSalesforceUrls){
        return null;
    }
    
    public static testMethod void testMapRequestUrl(){
        SiteUrlRewriter r = new SiteUrlRewriter();
        system.assertEquals(Page.formList.getUrl(), r.mapRequestUrl( new PageReference('/formList') ).getUrl() );
        system.assertEquals(Page.formList.getUrl(), r.mapRequestUrl( new PageReference('/apex/formlist') ).getUrl() );
        system.assertEquals(Page.Submission.getUrl(), r.mapRequestUrl( new PageReference('/Submission') ).getUrl() );
        
        PageReference otherPr = new PageReference('/other');
        system.assertEquals(otherPr.getUrl(), r.mapRequestUrl( otherPr ).getUrl());
    }
    
    public static testMethod void testGenerateUrlFor(){
        SiteUrlRewriter r = new SiteUrlRewriter();
        system.assertEquals(null, r.generateUrlFor( new PageReference[]{} ));
    }
}