/* assets/viddra-core.js
   Minimal core helpers for Viddra (no UI changes).
   - Keeps localStorage consistent via www redirect
   - Canonical token + profile read/migrate
*/

(function (w) {
  "use strict";

  var PROFILE_KEY = "viddra_profile_v1";
  var ONB_KEY = "viddra_onboarding_v1";

  function redirectToWwwIfNeeded() {
    try {
      if (location && location.hostname === "viddra.se") {
        location.replace("https://www.viddra.se" + location.pathname + location.search + location.hash);
        return true;
      }
    } catch (e) {}
    return false;
  }

  function makeToken() {
    return "t_" + Date.now().toString(36) + "_" + Math.random().toString(36).slice(2, 10);
  }

  function ensureToken() {
    var t = localStorage.getItem("viddra_token");
    if (!t) {
      t = makeToken();
      localStorage.setItem("viddra_token", t);
    }
    return t;
  }

  function safeJSONParse(s) {
    try { return JSON.parse(s); } catch (e) { return null; }
  }

  function normalizeBirthToken(tok) {
    tok = String(tok || "").trim();
    // ISO yyyy-mm-dd -> yyyymmdd
    var iso = tok.match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (iso) return iso[1] + iso[2] + iso[3];

    // yyyymmdd
    if (tok.match(/^\d{8}$/)) return tok;

    // yymmdd -> infer century (<=29 => 20xx else 19xx)
    if (tok.match(/^\d{6}$/)) {
      var yy = parseInt(tok.slice(0, 2), 10);
      var yyyy = (yy <= 29) ? (2000 + yy) : (1900 + yy);
      return String(yyyy) + tok.slice(2);
    }
    return "";
  }

  function looksLikeRealDate_yyyymmdd(s) {
    if (!s || !s.match(/^\d{8}$/)) return false;
    var y = parseInt(s.slice(0, 4), 10);
    var m = parseInt(s.slice(4, 6), 10);
    var d = parseInt(s.slice(6, 8), 10);
    if (y < 1900 || y > 2030) return false;
    if (m < 1 || m > 12) return false;
    if (d < 1 || d > 31) return false;
    return true;
  }

  function migrateProfileIfNeeded() {
    if (localStorage.getItem(PROFILE_KEY)) return;

    // 1) from viddra_onboarding_v1
    var onb = safeJSONParse(localStorage.getItem(ONB_KEY) || "");
    if (onb && typeof onb === "object" && Object.keys(onb).length) {
      var adultsN = parseInt(onb.adults || 1, 10);
      if (isNaN(adultsN) || adultsN < 1) adultsN = 1;
      if (adultsN > 2) adultsN = 2;

      var a1 = String(onb.adult1Name || "Vuxen 1");
      var a2 = String(onb.adult2Name || "Vuxen 2");

      var adultsArr = [{ name: a1 }];
      if (adultsN === 2) adultsArr.push({ name: a2 });

      var kids = [];
      var births = Array.isArray(onb.childrenBirthdates) ? onb.childrenBirthdates : [];
      births.forEach(function (b) {
        var norm = normalizeBirthToken(b);
        if (looksLikeRealDate_yyyymmdd(norm)) kids.push({ name: "", birth: norm });
      });

      var profile = {
        v: 1,
        createdAt: new Date().toISOString(),
        adults: adultsArr,
        children: kids,
        housingType: String(onb.housing || ""),
        flags: {
          hasPets: !!onb.hasPets,
          hasCar: !!onb.hasCar,
          hasSmallLoans: !!onb.hasSmallloans,
          hasCottage: !!onb.hasCottage
        },
        car: { count: parseInt(onb.carCount || 0, 10) || 0 },
        heating: { primary: "", extra: "" }
      };

      localStorage.setItem(PROFILE_KEY, JSON.stringify(profile));
      return;
    }

    // 2) from legacy separate keys
    var adultsRaw = safeJSONParse(localStorage.getItem("viddra_adults") || "");
    var childrenRaw = safeJSONParse(localStorage.getItem("viddra_children") || "");
    var housingType2 = localStorage.getItem("viddra_housingType") || "";
    var hasSmallLoans2 = localStorage.getItem("viddra_hasSmallLoans") === "1";

    var prof2 = {
      v: 1,
      createdAt: new Date().toISOString(),
      adults: [],
      children: [],
      housingType: String(housingType2 || ""),
      flags: { hasSmallLoans: !!hasSmallLoans2 },
      car: { count: 0 },
      heating: { primary: "", extra: "" }
    };

    if (Array.isArray(adultsRaw) && adultsRaw.length) {
      adultsRaw.forEach(function (a) {
        var nm = a && a.name ? String(a.name).trim() : "";
        if (nm) prof2.adults.push({ name: nm });
      });
    }
    if (!prof2.adults.length) prof2.adults = [{ name: "Vuxen 1" }];

    if (Array.isArray(childrenRaw)) {
      childrenRaw.forEach(function (c) {
        if (typeof c === "string") {
          var n1 = normalizeBirthToken(c);
          if (looksLikeRealDate_yyyymmdd(n1)) prof2.children.push({ name: "", birth: n1 });
        } else if (c && typeof c === "object") {
          var b2 = c.birth || c.dob || c.fodd || c.birthdate || c.born || "";
          var n2 = normalizeBirthToken(b2);
          if (looksLikeRealDate_yyyymmdd(n2)) prof2.children.push({ name: String(c.name || "").trim(), birth: n2 });
        }
      });
    }

    localStorage.setItem(PROFILE_KEY, JSON.stringify(prof2));
  }

  function readProfile() {
    migrateProfileIfNeeded();
    var s = localStorage.getItem(PROFILE_KEY);
    if (!s) return null;
    try { return JSON.parse(s) || null; } catch (e) { return null; }
  }

  // -----------------------------
  // NEW: Canonical profile helpers
  // -----------------------------

  function norm(s) {
    s = String(s || "").toLowerCase().trim();
    s = s.replace(/\s+/g, " ");
    s = s.replace(/å/g, "a").replace(/ä/g, "a").replace(/ö/g, "o");
    return s;
  }

  function getKidsCount(profile) {
    try {
      if (!profile) profile = readProfile();
      if (!profile) return 0;
      if (Array.isArray(profile.children)) return profile.children.length;
      return 0;
    } catch (e) { return 0; }
  }

  function getCarCount(profile) {
    try {
      if (!profile) profile = readProfile();
      if (!profile) return 0;

      // primary: profile.car.count
      if (profile.car && typeof profile.car.count !== "undefined") {
        var c = parseInt(profile.car.count || 0, 10);
        if (isNaN(c) || c < 0) c = 0;
        return c;
      }

      // fallback: flags.hasCar
      if (profile.flags && profile.flags.hasCar) return 1;

      return 0;
    } catch (e) { return 0; }
  }

  function getHousingType(profile) {
    try {
      if (!profile) profile = readProfile();
      if (!profile) return "";

      return norm(profile.housingType || "");
    } catch (e) { return ""; }
  }

  function isVilla(profile) {
    var ht = getHousingType(profile);
    return (ht.indexOf("villa") >= 0 || ht.indexOf("radhus") >= 0 || ht.indexOf("hus") >= 0);
  }

  function isHyra(profile) {
    var ht = getHousingType(profile);
    return (ht.indexOf("hyres") >= 0 || ht.indexOf("hyr") >= 0);
  }

  function isBrf(profile) {
    var ht = getHousingType(profile);
    return (ht.indexOf("bostads") >= 0 || ht.indexOf("brf") >= 0);
  }

  function monthKeyNow() {
    var d = new Date();
    return d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0");
  }

  function apiGet(url) {
    return fetch(url, { method: "GET", cache: "no-store" })
      .then(function (res) { return res.text().then(function (txt) { return { status: res.status, text: txt }; }); });
  }

  function apiPost(url, formBody) {
    return fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: formBody,
      cache: "no-store"
    }).then(function (res) { return res.text().then(function (txt) { return { status: res.status, text: txt }; }); });
  }

  w.ViddraCore = {
    redirectToWwwIfNeeded: redirectToWwwIfNeeded,
    ensureToken: ensureToken,
    readProfile: readProfile,

    // NEW helpers (canonical)
    getKidsCount: getKidsCount,
    getCarCount: getCarCount,
    getHousingType: getHousingType,
    isVilla: isVilla,
    isHyra: isHyra,
    isBrf: isBrf,

    monthKeyNow: monthKeyNow,
    apiGet: apiGet,
    apiPost: apiPost
  };
})(window);
