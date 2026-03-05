// viddra_calc.js
// Canonical budget calculations for Viddra.
// - Never changes storage keys
// - Works with both "flat" object and legacy {items:[...]} payloads
// - Uses the LOCKED viddra_budget_values_v1 keyset as the source of truth

(function(){
  "use strict";

  // --- Locked keys (do not change) ---
  const LOCKED_KEYS = [
    "income_salary1","income_salary2","income_child_allowance","income_study_allowance","income_child_support_in",
    "home_rent_fee","home_electricity","home_heating","home_water_sewer","home_waste","home_maintenance","home_insurance",
    "loan_small_total",
    "car_loan_leasing","car_fuel_charge","car_insurance","car_service_tax","car_parking",
    "transport_public","transport_taxi",
    "everyday_food","everyday_hygiene","everyday_clothes","everyday_streaming","everyday_mobile","everyday_internet",
    "kids_activities","kids_stuff","kids_allowance","everyday_child_support_out",
    "pet_food","pet_insurance","pet_supplies",
    "cottage_costs",
    "save_buffer","save_long","save_goals"
  ];

  const KEYSET = new Set(LOCKED_KEYS);

  function asNum(v){
    if (v === null || v === undefined) return 0;
    if (typeof v === "number" && isFinite(v)) return v;
    if (typeof v !== "string") return 0;
    const s = v.replace(/\s+/g,"").replace(/,/g,".").replace(/[^\d.\-]/g,"");
    const n = parseFloat(s);
    return isFinite(n) ? n : 0;
  }

  function isObject(x){
    return x && typeof x === "object" && !Array.isArray(x);
  }

  function toFlat(budget){
    const flat = {};
    if (!budget) return flat;

    if (isObject(budget) && isObject(budget.budget)) budget = budget.budget;

    if (isObject(budget) && Array.isArray(budget.items)) {
      for (const it of budget.items) {
        if (!it) continue;
        const k = String(it.key || it.id || it.name || "").trim();
        if (!k || !KEYSET.has(k)) continue;
        flat[k] = asNum(it.amount ?? it.value ?? it.val ?? 0);
      }
      return flat;
    }

    if (isObject(budget)) {
      for (const k of Object.keys(budget)) {
        if (!KEYSET.has(k)) continue;
        flat[k] = asNum(budget[k]);
      }
    }

    return flat;
  }

  function splitTotals(flat){
    let income = 0;
    let expenses = 0;
    let savings = 0;

    for (const k of Object.keys(flat)) {
      const amt = asNum(flat[k]);
      if (k.startsWith("income_")) income += amt;
      else if (k.startsWith("save_")) savings += amt;
      else expenses += amt;
    }
    return {income, expenses, savings, net: income - expenses - savings};
  }

  function groupExpenses(flat){
    const groups = {
      boende: 0,
      smalan: 0,
      bil: 0,
      transport: 0,
      vardag: 0,
      barn: 0,
      husdjur: 0,
      semesterbostad: 0,
      sparande: 0
    };

    for (const k of Object.keys(flat)) {
      const amt = asNum(flat[k]);

      if (k.startsWith("save_")) { groups.sparande += amt; continue; }
      if (k.startsWith("home_")) { groups.boende += amt; continue; }
      if (k.startsWith("loan_")) { groups.smalan += amt; continue; }
      if (k.startsWith("car_"))  { groups.bil += amt; continue; }
      if (k.startsWith("transport_")) { groups.transport += amt; continue; }
      if (k.startsWith("everyday_")) { 
        if (k === "everyday_child_support_out") groups.barn += amt;
        else groups.vardag += amt; 
        continue; 
      }
      if (k.startsWith("kids_")) { groups.barn += amt; continue; }
      if (k.startsWith("pet_"))  { groups.husdjur += amt; continue; }
      if (k.startsWith("cottage_")) { groups.semesterbostad += amt; continue; }
      groups.vardag += amt;
    }

    return groups;
  }

  function formatSEK(n){
    const x = Math.round(asNum(n));
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + " kr";
  }

  window.ViddraCalc = {
    LOCKED_KEYS,
    toFlat,
    splitTotals,
    groupExpenses,
    formatSEK,
    asNum
  };
})();