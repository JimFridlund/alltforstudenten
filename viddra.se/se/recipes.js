/* recipes.js – Viddra receptkälla (V1)
   Laddas via <script src="recipes.js"></script>
*/

window.VIDDRA_RECIPES = [
  {
    id: "kottfarssas",
    title: "Spaghetti & köttfärssås",
    image: "assets/recipes/kottfarssas.png",
    cuisine: "italienskt",
    tags: ["klassisk", "matlåda", "barnvänligt"],
    diet: ["kött"],
    allergens: ["gluten", "mjölkfri-möjlig"], // “möjlig” = kan anpassas
    cost: "normal", // budget/normal/lyx
    time: 30,
    portions: 4,
    ingredients: [
      ["spaghetti", "500 g"],
      ["nötfärs", "500 g"],
      ["gul lök", "1 st"],
      ["vitlök", "2 klyftor"],
      ["morot", "1 st"],
      ["krossade tomater", "2 burkar"],
      ["tomatpuré", "2 msk"],
      ["buljongtärning", "1 st"],
      ["olivolja", "1 msk"],
      ["oregano", "1 tsk"],
      ["salt", "1 tsk"],
      ["svartpeppar", "0,5 tsk"]
    ],
    steps: [
      "Hacka lök, vitlök och riv morot grovt. Fräs i olja 2–3 min.",
      "Bryn färsen. Rör ner tomatpuré, oregano, salt och peppar.",
      "Häll i krossade tomater + buljong (lite vatten vid behov). Sjud 15–20 min.",
      "Koka pastan. Servera med ev. parmesan."
    ]
  },

  {
    id: "flaskfilegryta",
    title: "Fläskfilégryta (gräddig)",
    image: "assets/recipes/flaskfilegryta.png",
    cuisine: "svenskt",
    tags: ["gryta", "helg", "mättande"],
    diet: ["kött"],
    allergens: ["mjölk"],
    cost: "lyx",
    time: 35,
    portions: 4,
    ingredients: [
      ["fläskfilé", "600 g"],
      ["champinjoner", "250 g"],
      ["gul lök", "1 st"],
      ["vitlök", "2 klyftor"],
      ["smör", "1 msk"],
      ["matlagningsgrädde", "3 dl"],
      ["crème fraiche", "2 dl"],
      ["kalvfond", "1 msk"],
      ["paprikapulver", "1 tsk"],
      ["salt", "1 tsk"],
      ["svartpeppar", "0,5 tsk"]
    ],
    steps: [
      "Skär fläskfilé i skivor. Salta/peppra och bryn snabbt i smör. Lägg åt sidan.",
      "Fräs lök, vitlök och champinjoner i samma panna.",
      "Tillsätt grädde, crème fraiche, fond och paprikapulver. Sjud 5–8 min.",
      "Lägg tillbaka köttet och sjud 3–5 min. Servera med ris/potatis."
    ]
  },

  {
    id: "korvstroganoff",
    title: "Korvstroganoff",
    image: "assets/recipes/korvstroganoff.png",
    cuisine: "svenskt",
    tags: ["snabbt", "barnvänligt", "vardag"],
    diet: ["kött"],
    allergens: ["mjölkfri-möjlig"],
    cost: "budget",
    time: 20,
    portions: 4,
    ingredients: [
      ["falukorv", "500 g"],
      ["gul lök", "1 st"],
      ["smör", "1 msk"],
      ["tomatpuré", "2 msk"],
      ["matlagningsgrädde", "3 dl"],
      ["paprikapulver", "1 tsk"],
      ["salt", "1 tsk"],
      ["svartpeppar", "0,5 tsk"]
    ],
    steps: [
      "Strimla korv och hacka lök. Fräs lök i smör.",
      "Lägg i korv och fräs 2–3 min. Rör ner tomatpuré + paprika.",
      "Häll i grädde och sjud 5 min. Smaka av."
    ]
  },

  {
    id: "biff_stroganoff",
    title: "Biff Stroganoff",
    image: "assets/recipes/biff_stroganoff.png",
    cuisine: "ryskt",
    tags: ["helg", "gryta"],
    diet: ["kött"],
    allergens: ["mjölk"],
    cost: "lyx",
    time: 30,
    portions: 4,
    ingredients: [
      ["lövbiff", "500 g"],
      ["gul lök", "1 st"],
      ["champinjoner", "250 g"],
      ["smör", "1 msk"],
      ["crème fraiche", "2 dl"],
      ["matlagningsgrädde", "2 dl"],
      ["tomatpuré", "1 msk"],
      ["dijonsenap", "1 msk"],
      ["salt", "1 tsk"],
      ["svartpeppar", "0,5 tsk"]
    ],
    steps: [
      "Strimla lövbiff. Bryn snabbt och lägg åt sidan.",
      "Fräs lök och svamp. Rör ner tomatpuré och senap.",
      "Tillsätt crème fraiche + grädde. Sjud 5 min.",
      "Lägg tillbaka köttet kort (1–2 min)."
    ]
  },

  {
    id: "lasagne",
    title: "Lasagne",
    image: "assets/recipes/lasagne.png",
    cuisine: "italienskt",
    tags: ["matlåda", "klassisk", "familj"],
    diet: ["kött"],
    allergens: ["gluten", "mjölk"],
    cost: "normal",
    time: 60,
    portions: 6,
    ingredients: [
      ["lasagneplattor", "1 paket"],
      ["nötfärs", "500 g"],
      ["gul lök", "1 st"],
      ["vitlök", "2 klyftor"],
      ["krossade tomater", "2 burkar"],
      ["smör", "50 g"],
      ["vetemjöl", "4 msk"],
      ["mjölk", "6 dl"],
      ["riven ost", "200 g"],
      ["salt", "1 tsk"],
      ["svartpeppar", "0,5 tsk"],
      ["oregano", "1 tsk"]
    ],
    steps: [
      "Gör köttfärssås med lök, vitlök och krossade tomater. Sjud 15 min.",
      "Gör bechamel: smält smör, vispa i mjöl, tillsätt mjölk och koka till tjock.",
      "Varva sås, plattor och bechamel. Toppa ost.",
      "Grädda 200°C ca 30–35 min. Vila 10 min."
    ]
  },

  {
    id: "kycklinggryta",
    title: "Kycklinggryta (krämig)",
    image: "assets/recipes/kycklinggryta.png",
    cuisine: "svenskt",
    tags: ["gryta", "vardag"],
    diet: ["kyckling"],
    allergens: ["mjölk"],
    cost: "normal",
    time: 30,
    portions: 4,
    ingredients: [
      ["kycklingfilé", "600 g"],
      ["gul lök", "1 st"],
      ["vitlök", "2 klyftor"],
      ["paprika", "1 st"],
      ["smör", "1 msk"],
      ["matlagningsgrädde", "3 dl"],
      ["crème fraiche", "2 dl"],
      ["kycklingfond", "1 msk"],
      ["salt", "1 tsk"],
      ["svartpeppar", "0,5 tsk"]
    ],
    steps: [
      "Bryn kycklingbitar. Lägg åt sidan.",
      "Fräs lök, vitlök och paprika.",
      "Tillsätt grädde, crème fraiche och fond. Sjud 5–8 min.",
      "Lägg i kycklingen och koka klart 3–5 min."
    ]
  },

  {
    id: "sjomansbiff",
    title: "Sjömansbiff",
    image: "assets/recipes/sjomansbiff.png",
    cuisine: "svenskt",
    tags: ["klassisk", "gryta"],
    diet: ["kött"],
    allergens: ["mjölkfri-möjlig"],
    cost: "normal",
    time: 75,
    portions: 4,
    ingredients: [
      ["nötkött (grytbitar)", "700 g"],
      ["potatis", "1,2 kg"],
      ["gul lök", "2 st"],
      ["köttbuljong", "7 dl"],
      ["lagerblad", "2 st"],
      ["smör", "1 msk"],
      ["salt", "1 tsk"],
      ["svartpeppar", "0,5 tsk"]
    ],
    steps: [
      "Skiva potatis och lök.",
      "Varva kött, lök och potatis i gryta. Häll på buljong. Lägg i lagerblad.",
      "Sjud under lock 60–70 min tills potatisen är mjuk.",
      "Smaka av med salt och peppar."
    ]
  },

  {
    id: "pasta_alfredo",
    title: "Pasta Alfredo",
    image: "assets/recipes/pasta_alfredo.png",
    cuisine: "italienskt",
    tags: ["snabbt", "krämigt"],
    diet: ["vegetariskt"],
    allergens: ["gluten", "mjölk"],
    cost: "normal",
    time: 20,
    portions: 4,
    ingredients: [
      ["fettuccine", "500 g"],
      ["smör", "60 g"],
      ["vispgrädde", "3 dl"],
      ["parmesan", "120 g"],
      ["vitlök", "1 klyfta"],
      ["salt", "1 tsk"],
      ["svartpeppar", "0,5 tsk"]
    ],
    steps: [
      "Koka pastan.",
      "Smält smör, fräs vitlök kort. Tillsätt grädde och sjud 2–3 min.",
      "Rör ner parmesan. Vänd ner pastan. Smaka av."
    ]
  },

  {
    id: "boeuf_bourguignon",
    title: "Boeuf Bourguignon",
    image: "assets/recipes/boeuf_bourguignon.png",
    cuisine: "franskt",
    tags: ["helg", "gryta"],
    diet: ["kött"],
    allergens: ["mjölkfri-möjlig"],
    cost: "lyx",
    time: 150,
    portions: 6,
    ingredients: [
      ["högrev", "900 g"],
      ["morot", "2 st"],
      ["gul lök", "1 st"],
      ["champinjoner", "250 g"],
      ["rött vin", "5 dl"],
      ["krossade tomater", "1 burk"],
      ["kalvfond", "1 msk"],
      ["lagerblad", "2 st"],
      ["salt", "1 tsk"],
      ["svartpeppar", "0,5 tsk"]
    ],
    steps: [
      "Bryn köttet i omgångar. Lägg i gryta.",
      "Fräs lök, morot och svamp. Lägg i grytan.",
      "Häll på vin, tomater, fond och kryddor. Sjud 2–2,5 h.",
      "Smaka av och servera med potatis/puré."
    ]
  },

  {
    id: "kyckling_i_ugn",
    title: "Kycklingfilé i ugn (vitlök & crème fraiche)",
    image: "assets/recipes/kyckling_i_ugn.png",
    cuisine: "svenskt",
    tags: ["enkelt", "vardag"],
    diet: ["kyckling"],
    allergens: ["mjölk"],
    cost: "normal",
    time: 35,
    portions: 4,
    ingredients: [
      ["kycklingfilé", "700 g"],
      ["crème fraiche", "3 dl"],
      ["vitlök", "2 klyftor"],
      ["riven ost", "100 g"],
      ["paprikapulver", "1 tsk"],
      ["salt", "1 tsk"],
      ["svartpeppar", "0,5 tsk"]
    ],
    steps: [
      "Lägg kycklingen i ugnsform. Salta/peppra.",
      "Blanda crème fraiche med pressad vitlök och paprika. Häll över.",
      "Toppa med ost. Ugn 200°C ca 25 min."
    ]
  },

  {
    id: "tacopaj",
    title: "Tacopaj",
    image: "assets/recipes/tacopaj.png",
    cuisine: "mexikanskt",
    tags: ["fredag", "familj"],
    diet: ["kött"],
    allergens: ["gluten", "mjölk"],
    cost: "normal",
    time: 45,
    portions: 6,
    ingredients: [
      ["pajdeg", "1 st"],
      ["nötfärs", "500 g"],
      ["tacokrydda", "1 påse"],
      ["crème fraiche", "2 dl"],
      ["riven ost", "200 g"],
      ["majs", "1 burk"],
      ["paprika", "1 st"]
    ],
    steps: [
      "Förgrädda pajskal 200°C 10 min.",
      "Stek färs och blanda med tacokrydda. Lägg i pajskalet.",
      "Toppa med crème fraiche, majs/paprika och ost. Grädda 20–25 min."
    ]
  },

  {
    id: "lovbiffpasta",
    title: "Lövbiffpasta",
    image: "assets/recipes/lovbiffpasta.png",
    cuisine: "svenskt",
    tags: ["snabbt", "vardag"],
    diet: ["kött"],
    allergens: ["gluten", "mjölkfri-möjlig"],
    cost: "normal",
    time: 25,
    portions: 4,
    ingredients: [
      ["lövbiff", "500 g"],
      ["pasta", "500 g"],
      ["gul lök", "1 st"],
      ["matlagningsgrädde", "3 dl"],
      ["soja", "1 msk"],
      ["smör", "1 msk"],
      ["salt", "1 tsk"],
      ["svartpeppar", "0,5 tsk"]
    ],
    steps: [
      "Koka pastan.",
      "Strimla lövbiff och lök. Bryn i smör.",
      "Tillsätt grädde och soja. Sjud 5 min. Vänd ner pastan."
    ]
  }
];
