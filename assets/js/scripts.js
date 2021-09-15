var uploaded_leads = {};
var uploaded_domains = {};

function convertToCSV(arr) {
    const array = [Object.keys(arr[0])].concat(arr)

    console.log('✅', array);

    return array.map(it => {
        console.log(it);
        return Object.values(it).toString()
    }).join('\n')
}

// Function that will contact directly with our backend in order to retrieve the data we want
const API_calls = async function(lead) {

    return new Promise((resolve, reject) => {
        fetch('post.php?type=email', {
            method: "POST",
            body: JSON.stringify({lead:lead}),
            headers: {"Content-type": "application/json;charset=UTF-8"}
        })
        .then(response =>  response.json() )
        .then(json => resolve(json))    //print data to console
        .catch(err => reject(err)); // Catch errors
        
    })   

}

var totalLeadsEnriched = [];
const enrich_leads = async function(a) {
    totalLeadsEnriched = [];

    $('.popup').show(500);
    var start = window.performance.now();

    console.log('...START...');

    for (let i = 0; i < uploaded_leads.leads.length; i++) {

        if (i>0) {
            if (i % 100 == 0) {
                await delay(30000);
            }
        }

        const result = await API_calls(uploaded_leads.leads[i]);
        console.log('✅',i,result);
        if (!result.error) {
            totalLeadsEnriched.push(result);
        }
    }
    
    var end = window.performance.now();
    console.log('...END...', totalLeadsEnriched);
    
    //Parsing data to cvs and downloading the document
    var csv = Papa.unparse(totalLeadsEnriched);

    var hiddenElement = document.createElement('a');
    hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
    hiddenElement.target = '_blank';
    hiddenElement.download = 'leads_processed_'+Date.now();+'.csv';
    hiddenElement.click();

    $('.total-enriched').text('Leads enriched: '+totalLeadsEnriched.length);
    $('.total-processed').text('Leads processed: '+uploaded_leads.leads.length);

    console.log('Execution time: ', end - start, ' ms');
    console.log('Execution time: ', (end - start)/1000 ,' s');
    console.log('Execution time: ', (end - start)/1000/60 ,' m');
    $('.execution-time').text('Execution time: '+ Math.floor((end - start)/1000) +' seconds');
    $('.popup').hide(500);

}


// Function that will contact directly with our backend in order to retrieve the data we want
const API_calls_company = async function(lead) {

    return new Promise((resolve, reject) => {
        fetch('post.php?type=domain', {
            method: "POST",
            body: JSON.stringify({lead:lead}),
            headers: {"Content-type": "application/json;charset=UTF-8"}
        })
        .then(response =>  response.json() )
        .then(json => resolve(json))    //print data to console
        .catch(err => reject(err)); // Catch errors
        
    })   

}

var totalCompaniesEnriched = [];
const enrich_companies = async function(a) {

    totalCompaniesEnriched = [];

    $('.popup').show(500);
    var start = window.performance.now();

    console.log('...START...', uploaded_domains.domains);

    for (let i = 0; i < uploaded_domains.domains.length; i++) {

        if (i>0) {
            if (i % 100 == 0) {
                await delay(30000);
            }
        }

        const result = await API_calls_company(uploaded_domains.domains[i]);
        console.log('✅',i,result);
        if (!result.error) {
            totalCompaniesEnriched.push(result);
        }
    }
    
    var end = window.performance.now();
    console.log('...END...', totalCompaniesEnriched);
    
    // Parsing data to cvs and downloading the document
    var csv = Papa.unparse(totalCompaniesEnriched);

    var hiddenElement = document.createElement('a');
    hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
    hiddenElement.target = '_blank';
    hiddenElement.download = 'companies_processed_'+Date.now();+'.csv';
    hiddenElement.click();

    $('.total-enriched').text('Companies enriched: '+totalCompaniesEnriched.length);
    $('.total-processed').text('Companies processed: '+uploaded_domains.domains.length);

    console.log('Execution time: ', end - start, ' ms');
    console.log('Execution time: ', (end - start)/1000 ,' s');
    console.log('Execution time: ', (end - start)/1000/60 ,' m');
    $('.execution-time').text('Execution time: '+ Math.floor((end - start)/1000) +' seconds');
    $('.popup').hide(500);

}
  
const delay = ms => new Promise(res => setTimeout(res, ms));


document.getElementById('import').onclick = function () {
    var files = document.getElementById('selectFiles_leads').files;

    if (files.length <= 0) {
        return false;
    }
    
    var fr = new FileReader();

    fr.onload = function (e) {

        var data = Papa.parse(e.target.result, {
            header: true
        });
        uploaded_leads.leads = data.data;

        console.log(data);
        // $("#import").attr("disabled", true);
        $("#enrich_leads").attr("disabled", false);
    }

    fr.readAsText(files.item(0));
    
};


document.getElementById('import_companies').onclick = function () {
    var files = document.getElementById('selectFiles_companies').files;

    if (files.length <= 0) {
        return false;
    }
    
    var fr = new FileReader();

    fr.onload = function (e) {

        var data = Papa.parse(e.target.result, {
            header: true
        });
        uploaded_domains.domains = data.data;

        console.log(data);
        // $("#import_companies").attr("disabled", true);
        $("#enrich_companies").attr("disabled", false);
    }


    fr.readAsText(files.item(0));
};