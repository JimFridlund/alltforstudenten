// budget.js (FIXED) - uses canonical calc (viddra_calc.js)
(function(){
  "use strict";
  const LS_BUDGET_PRIMARY = "viddra_budget_values_v1";
  const LS_ONBOARDING_PRIMARY = "viddra_onboarding_v1";
  const LS_USER_UUID = "viddra_user_id";

  function byId(id){ return document.getElementById(id); }
  function safeJSONParse(s){ try { return JSON.parse(s); } catch(e){ return null; } }
  function getOnboarding(){ return safeJSONParse(localStorage.getItem(LS_ONBOARDING_PRIMARY) || ""); }
  function getBudgetLocal(){ return safeJSONParse(localStorage.getItem(LS_BUDGET_PRIMARY) || ""); }
  function setText(id, text){ const el = byId(id); if (el) el.textContent = text; }

  function renderTotals(t){
    setText("sum_expenses", ViddraCalc.formatSEK(t.expenses));
    setText("sum_income", ViddraCalc.formatSEK(t.income));
    setText("sum_savings", ViddraCalc.formatSEK(t.savings));
    setText("sum_net", ViddraCalc.formatSEK(t.net));
  }
  function renderGroups(g){
    const pairs = [
      ["grp_boende",g.boende],["grp_smalan",g.smalan],["grp_bil",g.bil],["grp_transport",g.transport],
      ["grp_vardag",g.vardag],["grp_barn",g.barn],["grp_husdjur",g.husdjur],["grp_semesterbostad",g.semesterbostad],
      ["grp_sparande",g.sparande]
    ];
    for (const [id,val] of pairs) setText(id, ViddraCalc.formatSEK(val));
  }
  function renderBadges(o){
    const adults = (o && typeof o.adults === "number") ? o.adults : 1;
    const children = (o && Array.isArray(o.childrenBirthdates)) ? o.childrenBirthdates.length : 0;
    setText("badge_adults", adults === 1 ? "Vuxen 1" : "Vuxna " + adults);
    setText("badge_children", children + " barn");
  }
  function showLocalKeys(){
    setText("tech_onboarding_key", localStorage.getItem(LS_ONBOARDING_PRIMARY) ? LS_ONBOARDING_PRIMARY : "—");
    setText("tech_budget_key", localStorage.getItem(LS_BUDGET_PRIMARY) ? LS_BUDGET_PRIMARY : "—");
    setText("tech_user_key", localStorage.getItem(LS_USER_UUID) ? LS_USER_UUID : "—");
  }
  function attachNav(){
    const e = byId("btn_edit_budget");
    const r = byId("btn_fill_result");
    if (e) e.addEventListener("click", ()=> location.href="fill_budget.html");
    if (r) r.addEventListener("click", ()=> location.href="resultat.html");
  }

  async function loadBudget(){
    const statusEl = byId("db_status");
    if (statusEl) statusEl.textContent = "Budget laddar från DB … (fallback: localStorage)";
    const local = getBudgetLocal();
    const flat = ViddraCalc.toFlat(local);
    renderTotals(ViddraCalc.splitTotals(flat));
    renderGroups(ViddraCalc.groupExpenses(flat));
    if (statusEl) statusEl.textContent = "Budget laddad (localStorage).";
  }

  document.addEventListener("DOMContentLoaded", ()=>{
    attachNav();
    showLocalKeys();
    renderBadges(getOnboarding());
    loadBudget().catch(()=>{});
  });
})();