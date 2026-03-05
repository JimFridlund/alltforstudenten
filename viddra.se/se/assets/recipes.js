// assets/recipes.js (SAFE VERSION)
// Målet: alltid ge window.VIDDRA_RECIPES (200 st) utan dubletter.
// Bild: smart fallback (fil eller mapp) via onerror-kedja.

(function () {
  function tryImagePaths(slug) {
    return [
      "/assets/recipes/" + slug + ".jpg",
      "/assets/recipes/" + slug + ".jpeg",
      "/assets/recipes/" + slug + ".png",
      "/assets/recipes/" + slug + "/cover.jpg",
      "/assets/recipes/" + slug + "/cover.jpeg",
      "/assets/recipes/" + slug + "/cover.png",
      "/assets/recipes/" + slug + "/" + slug + ".jpg",
      "/assets/recipes/" + slug + "/" + slug + ".jpeg",
      "/assets/recipes/" + slug + "/" + slug + ".png",
      "/assets/recipes/" + slug + "/1.jpg",
      "/assets/recipes/" + slug + "/1.jpeg",
      "/assets/recipes/" + slug + "/1.png"
    ];
  }

  function attachSmartImage(img, slug) {
    var c = tryImagePaths(slug);
    var i = 0;
    img.src = c[i];
    img.onerror = function () {
      i++;
      if (i < c.length) img.src = c[i];
      else img.onerror = null;
    };
  }

  function slugify(s) {
    return String(s || "")
      .toLowerCase()
      .replace(/å/g, "a").replace(/ä/g, "a").replace(/ö/g, "o")
      .replace(/&/g, " och ")
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/-+/g, "-")
      .replace(/^-|-$/g, "");
  }

  function cuisineFromTitle(t) {
    var s = (t || "").toLowerCase();
    if (s.indexOf("taco") >= 0 || s.indexOf("ques") >= 0 || s.indexOf("enchil") >= 0 || s.indexOf("burrit") >= 0) return "mexikanskt";
    if (s.indexOf("pasta") >= 0 || s.indexOf("lasagne") >= 0 || s.indexOf("carbonara") >= 0 || s.indexOf("bolog") >= 0 || s.indexOf("alfredo") >= 0) return "italienskt";
    if (s.indexOf("curry") >= 0 || s.indexOf("tikka") >= 0 || s.indexOf("masala") >= 0) return "indiskt";
    if (s.indexOf("wok") >= 0 || s.indexOf("nudel") >= 0 || s.indexOf("ramen") >= 0 || s.indexOf("soja") >= 0 || s.indexOf("pad thai") >= 0) return "asiatiskt";
    if (s.indexOf("boeuf") >= 0 || s.indexOf("bourguignon") >= 0) return "franskt";
    return "svenskt";
  }

  function baseTags(t) {
    var s = (t || "").toLowerCase();
    var tags = ["vardag"];
    if (s.indexOf("snabb") >= 0) tags.push("snabbt");
    if (s.indexOf("matlåda") >= 0) tags.push("matlada");
    if (s.indexOf("korv") >= 0 || s.indexOf("köttbull") >= 0 || s.indexOf("pannk") >= 0) tags.push("barn");
    if (s.indexOf("gryta") >= 0 || s.indexOf("lasagne") >= 0 || s.indexOf("gratäng") >= 0) tags.push("matlada");
    return tags;
  }

  function estimateTimeCost(title) {
    var s = (title || "").toLowerCase();
    var time = 35;
    var cost = 35;

    if (s.indexOf("korv") >= 0) { time = 25; cost = 25; }
    if (s.indexOf("pannk") >= 0) { time = 25; cost = 20; }
    if (s.indexOf("soppa") >= 0) { time = 30; cost = 25; }
    if (s.indexOf("lax") >= 0 || s.indexOf("torsk") >= 0) { cost = 55; }
    if (s.indexOf("biff") >= 0 || s.indexOf("entrecote") >= 0) { cost = 70; }
    if (s.indexOf("lasagne") >= 0 || s.indexOf("gratäng") >= 0 || s.indexOf("ugn") >= 0) { time = 50; }
    if (s.indexOf("bourguignon") >= 0) { time = 120; cost = 60; }

    return { time: time, cost: cost };
  }

  function makeGeneric(title) {
    var id = slugify(title);
    var est = estimateTimeCost(title);
    return {
      id: id,
      title: title,
      cuisine: cuisineFromTitle(title),
      time: est.time,
      cost: est.cost,
      portions: 4,
      desc: "Vanlig vardagsmat i svenska hem. Vi fyller på med bilder och förbättrar texterna löpande.",
      tags: baseTags(title),
      ingredients: [
        "Huvudråvara (enligt titel)",
        "Lök/vitlök",
        "Kryddor (salt/peppar + valfria)",
        "Bas (ris/pasta/potatis)",
        "Sås/tillbehör"
      ],
      steps: [
        "Förbered råvaror (hacka, skär).",
        "Tillaga huvudråvaran och bygg smak (krydda).",
        "Gör sås/gryta/ugnsmoment enligt rätten.",
        "Koka basen (ris/pasta/potatis).",
        "Smaka av och servera."
      ]
    };
  }

  // === Dina 12 mappar/recept (de du visade på bilden) ===
  // Obs: id här ska matcha dina bildmappar/filnamn.
  var MANUAL = [
    { id: "biffstroganoff", title: "Biff Stroganoff", cuisine:"svenskt", time:35, cost:55, portions:4, desc:"Krämig stroganoff med biff och svamp.", tags:["vardag"], ingredients:["500 g biff i strimlor","1 lök","250 g champinjoner","2 dl grädde","1 msk dijon","Salt & peppar","Ris/pasta"], steps:["Bryn biff snabbt.","Stek lök och svamp.","Häll i grädde + dijon, sjud 5 min.","Lägg tillbaka biff 2–3 min.","Servera."] },
    { id: "boeufbourguignon", title: "Boeuf Bourguignon", cuisine:"franskt", time:120, cost:60, portions:6, desc:"Klassiskt långkok – ännu bättre dagen efter.", tags:["vardag","matlada"], ingredients:["1 kg högrev","2 morötter","1 lök","250 g svamp","Rödvin/buljong","Timjan/lagerblad","Salt & peppar"], steps:["Bryn köttet.","Fräs grönsaker.","Häll på vin/buljong + kryddor.","Sjud 1,5–2 h.","Servera med potatis/puré."] },
    { id: "chiliconcarne", title: "Chili con carne", cuisine:"mexikanskt", time:35, cost:25, portions:4, desc:"Billigt, matigt och perfekt att göra mycket av.", tags:["vardag","matlada"], ingredients:["400 g nötfärs","1 burk bönor","1 burk tomat","1 lök","Chilikrydda","Salt & peppar"], steps:["Stek lök och färs.","Tillsätt kryddor, tomat och bönor.","Sjud 15–20 min.","Servera med ris/nachos."] },
    { id: "fettuccinealfredo", title: "Fettuccine Alfredo", cuisine:"italienskt", time:20, cost:30, portions:4, desc:"Krämig pasta på få ingredienser.", tags:["vardag","snabbt"], ingredients:["Pasta","2 dl grädde","50 g smör","Parmesan","Salt & peppar"], steps:["Koka pasta.","Sjud grädde + smör.","Rör i parmesan.","Vänd runt pastan."] },
    { id: "flaskfilegryta", title: "Fläskfilégryta", cuisine:"svenskt", time:35, cost:40, portions:4, desc:"Krämig fläskfilégryta med svamp.", tags:["vardag","matlada"], ingredients:["500 g fläskfilé","250 g svamp","1 lök","2 dl grädde","1 dl crème fraiche","Dijon","Salt & peppar"], steps:["Bryn kött snabbt.","Stek lök/svamp.","Häll i grädde + crème fraiche.","Lägg tillbaka kött, sjud kort.","Servera."] },
    { id: "gravadlaxspenatsas", title: "Gravad lax med spenatsås", cuisine:"svenskt", time:25, cost:55, portions:4, desc:"Snabbt och lite festligt.", tags:["vardag"], ingredients:["Lax","200 g spenat","2 dl grädde","1 vitlök","Salt & peppar","Pasta/potatis"], steps:["Fräs spenat.","Sjud med grädde + vitlök.","Värm lax försiktigt.","Servera."] },
    { id: "korvstroganoff", title: "Korvstroganoff", cuisine:"svenskt", time:25, cost:25, portions:4, desc:"Barnfavorit och budgetvinnare.", tags:["vardag","barn","matlada"], ingredients:["Falukorv","Lök","Tomatpuré","Crème fraiche/grädde","Salt & peppar","Ris"], steps:["Stek korv + lök.","Rör i tomatpuré.","Tillsätt crème fraiche/grädde.","Sjud 5–8 min.","Servera med ris."] },
    { id: "kottfarssas", title: "Köttfärssås och spaghetti", cuisine:"italienskt", time:30, cost:25, portions:4, desc:"Trygg klassiker – perfekt matlåda.", tags:["vardag","barn","matlada"], ingredients:["Nötfärs","Lök","Vitlök","Krossade tomater","Tomatpuré","Kryddor","Spaghetti"], steps:["Fräs lök/vitlök.","Stek färs.","Rör i tomatpuré + kryddor.","Tillsätt tomater, puttra.","Servera."] },
    { id: "kycklinggryta", title: "Kycklinggryta", cuisine:"svenskt", time:30, cost:35, portions:4, desc:"Snabb gryta som går att variera.", tags:["vardag","matlada"], ingredients:["Kyckling","Lök","Grädde","Crème fraiche","Salt & peppar"], steps:["Stek kyckling + lök.","Tillsätt grädde/crème fraiche.","Sjud 10 min.","Servera med ris."] },
    { id: "kycklingmedvitlok", title: "Kyckling i ugn med vitlök & crème fraiche", cuisine:"svenskt", time:35, cost:35, portions:4, desc:"Allt-i-form. Saftigt och enkelt.", tags:["vardag","matlada","barn"], ingredients:["Kycklingfilé","Crème fraiche","Vitlök","Salt & peppar"], steps:["200°C ugn.","Lägg kyckling i form.","Blanda crème fraiche + vitlök, häll över.","In 20–25 min.","Servera."] },
    { id: "lasagne", title: "Lasagne", cuisine:"italienskt", time:60, cost:45, portions:6, desc:"Klassiker som räcker länge.", tags:["vardag","matlada"], ingredients:["Färs","Tomat","Plattor","Béchamel","Ost"], steps:["Gör sås.","Varva i form.","Toppa ost.","Grädda 30–40 min.","Vila 10 min."] },
    { id: "sjomansbiff", title: "Sjömansbiff", cuisine:"svenskt", time:60, cost:35, portions:4, desc:"Husman med potatis, lök och kött.", tags:["vardag","matlada"], ingredients:["Nötkött","Potatis","Lök","Öl/buljong","Kryddor"], steps:["Varva allt i gryta.","Häll på vätska.","Sjud under lock 45–60 min.","Servera."] }
  ];

  // === Vanliga svenska rätter (unika titlar) ===
  var BASE_TITLES = [
    "Köttbullar med potatis och gräddsås",
    "Pytt i panna med ägg",
    "Pannkakor med sylt",
    "Ugnspannkaka med fläsk",
    "Ärtsoppa",
    "Raggmunk med fläsk",
    "Falukorv i ugn",
    "Stekt falukorv med makaroner",
    "Kasslergratäng",
    "Flygande Jacob",
    "Kålpudding",
    "Kåldolmar",
    "Köttfärslimpa med brunsås",
    "Wallenbergare med potatispuré",
    "Isterband med dillstuvad potatis",
    "Fiskgratäng med potatismos",
    "Panerad torsk med remoulad",
    "Fiskpinnar med potatis",
    "Torsk i ugn med citron",
    "Lax i ugn med romsås",
    "Laxpasta med citron",
    "Kycklingwok",
    "Kyckling fajitas",
    "Tacos med köttfärs",
    "Tacopaj",
    "Quesadillas med ost och skinka",
    "Nachos i ugn",
    "Hamburgare med pommes",
    "Kebab i pitabröd",
    "Pizza på plåt",
    "Tomatsoppa",
    "Potatis- och purjolökssoppa",
    "Svampsoppa",
    "Gulaschsoppa",
    "Minestronesoppa",
    "Vegetarisk chili",
    "Linsgryta",
    "Halloumisallad",
    "Omelett med ost och skinka",
    "Äggröra och bacon",
    "Spaghetti carbonara",
    "Pasta med skinksås",
    "Pasta pesto med kyckling",
    "Pasta med tomatsås"
  ];

  // Fyll upp till 200 med “vanliga variationer” (unika)
  var PROTEINS = ["kyckling", "nötfärs", "fläsk", "lax", "torsk", "korv", "vegetarisk"];
  var BASES = ["gryta", "pasta", "wok", "ugn", "soppa", "sallad"];
  var FLAVORS = ["med curry", "med gräddsås", "med tomatsås", "med vitlök", "med paprika", "med svamp", "med citron", "med pesto", "med chili", "med soja och ingefära", "med parmesan"];

  var titles = [];
  var seenT = {};

  function addTitle(t) {
    if (!t) return;
    if (seenT[t]) return;
    seenT[t] = true;
    titles.push(t);
  }

  // Kuraterade först
  for (var a = 0; a < BASE_TITLES.length; a++) addTitle(BASE_TITLES[a]);

  // Generera tills vi når 200 (räknar med att manual också ingår)
  var guard = 0;
  while (titles.length < 220 && guard < 5000) { // 220 för säkerhetsmarginal, vi tar 200 efter merge
    var p = PROTEINS[guard % PROTEINS.length];
    var b = BASES[(guard * 3) % BASES.length];
    var f = FLAVORS[(guard * 7) % FLAVORS.length];

    var t = "";
    if (b === "pasta") t = "Pasta med " + p + " " + f;
    else if (b === "gryta") t = p.charAt(0).toUpperCase() + p.slice(1) + "gryta " + f;
    else if (b === "wok") t = "Wok med " + p + " " + f;
    else if (b === "ugn") t = p.charAt(0).toUpperCase() + p.slice(1) + " i ugn " + f;
    else if (b === "sallad") t = p.charAt(0).toUpperCase() + p.slice(1) + "sallad " + f;
    else t = p.charAt(0).toUpperCase() + p.slice(1) + "soppa " + f;

    // små fixar
    t = t.replace("Vegetariskgryta", "Vegetarisk gryta")
         .replace("vegetariskgryta", "Vegetarisk gryta");

    addTitle(t);
    guard++;
  }

  // Bygg auto-recept från titlar (unika)
  var AUTO = [];
  for (var i = 0; i < titles.length; i++) {
    AUTO.push(makeGeneric(titles[i]));
  }

  // Merge (manual vinner på id)
  var mapById = {};
  for (var j = 0; j < AUTO.length; j++) mapById[AUTO[j].id] = AUTO[j];
  for (var k = 0; k < MANUAL.length; k++) mapById[MANUAL[k].id] = MANUAL[k];

  // Bygg final med dublett-kontroll (id + title)
  var usedId = {};
  var usedTitle = {};
  var FINAL = [];

  function pushUnique(r) {
    if (usedId[r.id]) throw new Error("DUPLICATE id: " + r.id);
    if (usedTitle[r.title]) throw new Error("DUPLICATE title: " + r.title);
    usedId[r.id] = true;
    usedTitle[r.title] = true;
    FINAL.push(r);
  }

  // manual först (stabilt)
  for (var m = 0; m < MANUAL.length; m++) pushUnique(MANUAL[m]);

  // sedan resten
  for (var id in mapById) {
    if (mapById.hasOwnProperty(id)) {
      // hoppa om redan pushad
      if (usedId[id]) continue;
      pushUnique(mapById[id]);
    }
  }

  // exakt 200
  var RECIPES = FINAL.slice(0, 200);
  if (RECIPES.length !== 200) throw new Error("RECIPES not 200, got " + RECIPES.length);

  window.VIDDRA_RECIPES = RECIPES;
  window.VIDDRA_ATTACH_SMART_IMAGE = attachSmartImage;
})();
