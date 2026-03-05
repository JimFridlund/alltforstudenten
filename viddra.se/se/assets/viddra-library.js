/* assets/viddra-library.js
   Innehåller resurslistan för Bibliotek-sidan.
   Du fyller på VIDDRA_LIBRARY_ITEMS med fler poster över tid.
*/

(function(){
  window.VIDDRA_LIBRARY_ITEMS = [
    {
      id:"pdf_forsta_budget_v1",
      type:"pdf",
      topic:"spar",
      title:"Din första budget: 7 steg som faktiskt funkar",
      desc:"En snabbguide som tar dig från “kaos” till en budget som håller – utan Excel.",
      url:"bibliotek/pdf/Viddra_PDF_Din_forsta_budget_v1.pdf",
      tags:["ensam","par","barn","ingenbarn","villa","brf","hyra","bil","ingenbil"],
      minutes:12
    },
    {
      id:"pdf_abonnemang_v1",
      type:"pdf",
      topic:"abonnemang",
      title:"Abonnemangsstädning: spara 300–900 kr/mån",
      desc:"Checklista + plan. Gör det på 30 minuter och få en besparing som håller varje månad.",
      url:"bibliotek/pdf/Viddra_PDF_Abonnemangsstadning_v1.pdf",
      tags:["ensam","par","barn","ingenbarn","villa","brf","hyra","bil","ingenbil"],
      minutes:10
    },
    {
      id:"buffert_pdf_1",
      type:"pdf",
      topic:"spar",
      title:"Buffert på 30 dagar – så gör du",
      desc:"En enkel guide: hur stor buffert du behöver, hur du bygger den, och hur du håller den.",
      url:"bibliotek/pdf/Viddra_PDF_Mall_Buffert_pa_30_dagar_v1.pdf",
      tags:["ensam","par","barn","ingenbarn","villa","brf","hyra","bil","ingenbil"],
      minutes:8
    },
    {
      id:"abonn_pod_1",
      type:"pod",
      topic:"abonnemang",
      title:"Abonnemangsfällan",
      desc:"Kort poddavsnitt om hur ‘små’ kostnader blir stora över tid – och hur du städar på 30 minuter.",
      url:"#",
      tags:["ensam","par","barn","ingenbarn","villa","brf","hyra","bil","ingenbil"],
      minutes:18
    },
    {
      id:"boende_video_1",
      type:"video",
      topic:"boende",
      title:"Boendekostnad: vad ska räknas med?",
      desc:"Förklaring (video): hyra/avgift, drift, ränta, underhåll – och varför folk underskattar villan.",
      url:"#",
      tags:["villa","brf","hyra","ensam","par","barn","ingenbarn","bil","ingenbil"],
      minutes:12
    },
    {
      id:"skuld_article_1",
      type:"article",
      topic:"skuld",
      title:"Ränta & amortering på begripligt språk",
      desc:"En kort artikel om vad som faktiskt händer när räntan ändras – och hur du prioriterar rätt.",
      url:"#",
      tags:["ensam","par","barn","ingenbarn","villa","brf","hyra","bil","ingenbil"],
      minutes:6
    }
  ];

  window.VIDDRA_LIBRARY_TYPE_LABEL = function(t){
    if (t === "pod") return "Podd";
    if (t === "newsletter") return "Nyhetsbrev";
    if (t === "pdf") return "PDF";
    if (t === "video") return "Video";
    if (t === "article") return "Artikel";
    return t || "Resurs";
  };

  window.VIDDRA_LIBRARY_TOPIC_LABEL = function(t){
    if (t === "boende") return "Boende";
    if (t === "mat") return "Mat";
    if (t === "bil") return "Bil";
    if (t === "abonnemang") return "Abonnemang";
    if (t === "forsakring") return "Försäkring";
    if (t === "spar") return "Spara & buffert";
    if (t === "skuld") return "Lån & skulder";
    return t || "";
  };
})();