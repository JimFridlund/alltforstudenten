// assets/recipes.js
// En källa för all data (lista + detalj).
// Bilder: vi provar flera vägar automatiskt (fil eller mapp).

(function () {
  function tryImagePaths(slug) {
    return [
      // Vanliga: fil direkt
      `/assets/recipes/${slug}.jpg`,
      `/assets/recipes/${slug}.jpeg`,
      `/assets/recipes/${slug}.png`,

      // Om du har mappar per recept:
      `/assets/recipes/${slug}/cover.jpg`,
      `/assets/recipes/${slug}/cover.jpeg`,
      `/assets/recipes/${slug}/cover.png`,

      `/assets/recipes/${slug}/${slug}.jpg`,
      `/assets/recipes/${slug}/${slug}.jpeg`,
      `/assets/recipes/${slug}/${slug}.png`,

      // “första bilden”-varianter
      `/assets/recipes/${slug}/1.jpg`,
      `/assets/recipes/${slug}/1.jpeg`,
      `/assets/recipes/${slug}/1.png`
    ];
  }

  // 12 recept du visade i listan (kan byggas på vecka för vecka)
  const RECIPES = [
    {
      id: "kottfarssas",
      title: "Köttfärssås och spaghetti",
      time: 30,
      cost: 25,
      desc: "En trygg klassiker som funkar varje tisdag. Perfekt för matlåda.",
      portions: 4,
      ingredients: [
        "400 g nötfärs",
        "1 gul lök",
        "2 vitlöksklyftor",
        "1 burk krossade tomater",
        "2 msk tomatpuré",
        "1 tsk oregano",
        "1 tsk basilika",
        "Salt & peppar",
        "Spaghetti"
      ],
      steps: [
        "Hacka lök och vitlök. Fräs mjukt i lite olja.",
        "Tillsätt färs och stek tills den är genomstekt.",
        "Rör i tomatpuré och kryddor.",
        "Häll på krossade tomater. Låt puttra 15–20 min.",
        "Smaka av. Servera med spaghetti."
      ]
    },
    {
      id: "flaskfilegryta",
      title: "Fläskfilégryta med svamp och grädde",
      time: 35,
      cost: 40,
      desc: "Krämig och “helgkänsla” men ändå lätt vardagsmat.",
      portions: 4,
      ingredients: [
        "500 g fläskfilé",
        "250 g champinjoner",
        "1 gul lök",
        "2 dl grädde (eller matlagningsgrädde)",
        "1 dl crème fraiche",
        "1 msk dijonsenap",
        "Salt & peppar",
        "Smör/olja"
      ],
      steps: [
        "Skär fläskfilén i strimlor. Salta/peppra. Bryn snabbt, lägg åt sidan.",
        "Stek lök och svamp tills de får färg.",
        "Häll i grädde, crème fraiche och dijon. Låt sjuda 5–8 min.",
        "Lägg tillbaka köttet och sjud 3–5 min (inte för länge).",
        "Servera med ris/potatis/pasta."
      ]
    },
    {
      id: "kycklingmedvitlok",
      title: "Kycklingfilé i ugn med vitlök & crème fraiche",
      time: 35,
      cost: 35,
      desc: "Allt-i-form. Saftig kyckling med vitlök och krämig sås.",
      portions: 4,
      ingredients: [
        "600 g kycklingfilé",
        "2 dl crème fraiche",
        "2 dl grädde (valfritt – kan bytas mot mjölk)",
        "2–3 vitlöksklyftor",
        "1 tsk salt",
        "1 krm svartpeppar",
        "Ev. paprika/örter"
      ],
      steps: [
        "Sätt ugnen på 200°C.",
        "Lägg kycklingen i ugnsform. Salta/peppra.",
        "Blanda crème fraiche, grädde och pressad vitlök. Häll över.",
        "Tillaga 20–25 min tills kycklingen är klar.",
        "Servera med ris/pasta/potatis."
      ]
    },
    {
      id: "korvstroganoff",
      title: "Korvstroganoff",
      time: 25,
      cost: 25,
      desc: "Barnfavorit och budgetvinnare. Blir ännu bättre som matlåda.",
      portions: 4,
      ingredients: [
        "400 g falukorv",
        "1 gul lök",
        "2 msk tomatpuré",
        "2 dl crème fraiche/grädde",
        "1 dl mjölk/vatten",
        "Salt & peppar",
        "Ris"
      ],
      steps: [
        "Skär korv i strimlor. Stek med lök.",
        "Rör ner tomatpuré och fräs 1 minut.",
        "Tillsätt crème fraiche/grädde + lite mjölk/vatten.",
        "Låt sjuda 5–8 min. Smaka av.",
        "Servera med ris."
      ]
    },
    {
      id: "biffstroganoff",
      title: "Biff Stroganoff",
      time: 35,
      cost: 55,
      desc: "Lyxigare stroganoff med biff, svamp och krämig sås.",
      portions: 4,
      ingredients: [
        "500 g lövbiff/innanlår i strimlor",
        "250 g champinjoner",
        "1 gul lök",
        "2 dl grädde",
        "1 msk dijonsenap",
        "Salt & peppar",
        "Ris/pasta"
      ],
      steps: [
        "Bryn biff snabbt på hög värme, lägg åt sidan.",
        "Stek lök och svamp.",
        "Tillsätt grädde + dijon. Låt sjuda 5 min.",
        "Lägg tillbaka biff, sjud 2–3 min.",
        "Servera direkt."
      ]
    },
    {
      id: "lasagne",
      title: "Lasagne",
      time: 60,
      cost: 45,
      desc: "Klassisk lasagne som räcker länge. Perfekt att frysa in.",
      portions: 6,
      ingredients: [
        "500 g nötfärs",
        "1 gul lök",
        "1 burk krossade tomater",
        "2 msk tomatpuré",
        "Lasagneplattor",
        "Béchamel (mjölk + smör + mjöl) eller färdig",
        "Ost",
        "Salt & peppar"
      ],
      steps: [
        "Gör köttfärssås och låt puttra 15 min.",
        "Varva sås, plattor och béchamel i form.",
        "Toppa med ost.",
        "Grädda 30–40 min i 200°C.",
        "Låt vila 10 min före servering."
      ]
    },
    {
      id: "kycklinggryta",
      title: "Kycklinggryta",
      time: 30,
      cost: 35,
      desc: "Snabb kycklinggryta som du kan variera med curry, paprika eller svamp.",
      portions: 4,
      ingredients: [
        "600 g kyckling",
        "1 gul lök",
        "2 dl grädde",
        "1 dl crème fraiche",
        "1 tsk salt",
        "1 krm peppar",
        "Ev. curry/paprika"
      ],
      steps: [
        "Stek kyckling och lök.",
        "Häll i grädde + crème fraiche.",
        "Krydda och låt sjuda 10 min.",
        "Servera med ris."
      ]
    },
    {
      id: "sjomansbiff",
      title: "Sjömansbiff",
      time: 60,
      cost: 35,
      desc: "Svensk husman med potatis, lök och kött – långkok som sköter sig själv.",
      portions: 4,
      ingredients: [
        "600 g nötkött i bitar",
        "8 potatisar",
        "2 gula lökar",
        "2–3 dl öl/buljong",
        "Salt, peppar, lagerblad"
      ],
      steps: [
        "Varva potatis, lök och kött i gryta.",
        "Häll på öl/buljong och kryddor.",
        "Sjud under lock 45–60 min.",
        "Smaka av och servera."
      ]
    },
    {
      id: "chiliconcarne",
      title: "Chili con carne",
      time: 35,
      cost: 25,
      desc: "Billigt, matigt och perfekt att göra mycket av.",
      portions: 4,
      ingredients: [
        "400 g nötfärs",
        "1 burk kidneybönor",
        "1 burk krossade tomater",
        "1 gul lök",
        "Chilikrydda/spiskummin",
        "Salt & peppar"
      ],
      steps: [
        "Stek lök och färs.",
        "Tillsätt kryddor, tomater och bönor.",
        "Sjud 15–20 min.",
        "Servera med ris/nachos."
      ]
    },
    {
      id: "fettuccinealfredo",
      title: "Fettuccine Alfredo",
      time: 20,
      cost: 30,
      desc: "Krämig pasta på få ingredienser. Bäst nygjord.",
      portions: 4,
      ingredients: [
        "400 g fettuccine/pasta",
        "2 dl grädde",
        "50 g smör",
        "1 dl parmesan",
        "Salt & peppar"
      ],
      steps: [
        "Koka pastan.",
        "Smält smör, häll i grädde och låt sjuda.",
        "Rör ner parmesan.",
        "Vänd runt pastan. Smaka av."
      ]
    },
    {
      id: "boeufbourguignon",
      title: "Boeuf Bourguignon",
      time: 120,
      cost: 60,
      desc: "Klassiskt franskt långkok. Gör en stor sats – blir bättre dagen efter.",
      portions: 6,
      ingredients: [
        "1 kg högrev i bitar",
        "2 morötter",
        "1 gul lök",
        "250 g champinjoner",
        "Rödvin/buljong",
        "Timjan/lagerblad",
        "Salt & peppar"
      ],
      steps: [
        "Bryn köttet, lägg åt sidan.",
        "Fräs lök/morot/svamp.",
        "Häll på vin/buljong och kryddor.",
        "Låt sjuda 1,5–2 timmar.",
        "Servera med potatis/puré."
      ]
    },
    {
      id: "gravadlaxspenatsas",
      title: "Gravad lax med spenatsås",
      time: 25,
      cost: 55,
      desc: "Snabbt och lite festligt. Spenatsås + lax = hemma-lyx.",
      portions: 4,
      ingredients: [
        "400–500 g lax (gravad eller ugnsbakad)",
        "200 g spenat",
        "2 dl grädde",
        "1 vitlöksklyfta",
        "Salt & peppar",
        "Pasta/potatis"
      ],
      steps: [
        "Fräs spenat snabbt.",
        "Tillsätt grädde + vitlök, sjud 5 min.",
        "Lägg i laxen (värm försiktigt).",
        "Servera med pasta/potatis."
      ]
    }
  ];

  // Koppla onerror-chain för bilder
  function attachSmartImage(img, slug) {
    const candidates = tryImagePaths(slug);
    let i = 0;
    img.src = candidates[i];

    img.onerror = function () {
      i++;
      if (i < candidates.length) img.src = candidates[i];
      else img.onerror = null; // ge upp
    };
  }

  window.VIDDRA_RECIPES = RECIPES;
  window.VIDDRA_ATTACH_SMART_IMAGE = attachSmartImage;
})();
