<?php
$meta_title = "Om AlltFörStudenten – Sveriges plattform för studenten";
$meta_desc  = "AlltFörStudenten.se samlar checklistor, inspiration och lokala företag inför studenten. Läs mer om hur vi hjälper elever och företag över hela Sverige.";
$meta_kw    = "om allt för studenten, om oss studenten, studentplattform sverige";
?>

<?php $this->load->view('_header'); ?>

<style>
.omWrap{
  max-width:1100px;
  margin:0 auto;
  padding:40px 18px 60px;
}

.omHero{
  max-width:760px;
  margin-bottom:40px;
}

.omHero h1{
  font-family: Fraunces, Georgia, serif;
  font-size:38px;
  line-height:1.1;
  margin:0 0 14px;
}

.omHero p{
  font-size:17px;
  line-height:1.6;
  color:#5b6472;
  margin:0;
}

.omGrid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:40px;
  margin-top:40px;
}

.omCard{
  background:#ffffff;
  border:1px solid #e6eaf0;
  border-radius:16px;
  padding:26px;
}

.omCard h2{
  font-family: Fraunces, Georgia, serif;
  font-size:22px;
  margin:0 0 12px;
}

.omCard p{
  color:#5b6472;
  line-height:1.6;
  margin:0 0 14px;
}

.omValues{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:24px;
  margin-top:50px;
}

.omValue{
  background:#f7f9fc;
  padding:22px;
  border-radius:14px;
}

.omValue strong{
  display:block;
  font-size:15px;
  margin-bottom:6px;
}

/* NEW: personal story block */
.omStory{
  margin-top:40px;
}
.omStory p:last-child{
  margin-bottom:0;
}

.omCta{
  margin-top:60px;
  padding:40px;
  background:#0b2a4a;
  border-radius:18px;
  color:#ffffff;
}

.omCta h2{
  font-family: Fraunces, Georgia, serif;
  margin:0 0 10px;
  font-size:26px;
}

.omCta p{
  margin:0;
  line-height:1.6;
  opacity:.95;
}

.omBtn{
  display:inline-block;
  margin-top:18px;
  padding:12px 20px;
  border-radius:10px;
  background:#f5c542;
  font-weight:900;
  text-decoration:none;
  color:#132033;
}

@media(max-width:900px){
  .omGrid{
    grid-template-columns:1fr;
  }
  .omValues{
    grid-template-columns:1fr;
  }
}
</style>

<div class="omWrap">

  <div class="omHero">
    <h1>Vi gör studenten enklare</h1>
    <p>
      AlltFörStudenten.se är en nationell plattform som samlar checklistor,
      inspiration och lokala företag inför studenten.
      Vårt mål är att göra planeringen enklare – både för elever och föräldrar.
    </p>
  </div>

  <div class="omGrid">

    <div class="omCard">
      <h2>För elever &amp; familjer</h2>
      <p>
        Studenten är en av livets största milstolpar.
        Samtidigt innebär den många beslut – bal, flak, mottagning,
        kläder, fotograf, catering och presenter.
      </p>
      <p>
        Vi samlar informationen på ett ställe
        och gör det enkelt att hitta rätt lokalt.
      </p>
    </div>

    <div class="omCard">
      <h2>För företag</h2>
      <p>
        Vi erbjuder en exklusiv plats per kommun och kategori.
        Det betyder mindre konkurrens och tydligare synlighet.
      </p>
      <p>
        Vårt fokus är kvalitet framför kvantitet –
        en aktör per lista, inte tio.
      </p>
    </div>

  </div>

  <div class="omValues">
    <div class="omValue">
      <strong>Tydlighet</strong>
      Vi tror på enkel navigation och raka besked.
    </div>
    <div class="omValue">
      <strong>Lokal närvaro</strong>
      Varje kommun har sin egen sida – med lokala alternativ.
    </div>
    <div class="omValue">
      <strong>Långsiktighet</strong>
      Vi bygger för framtida årskullar, inte bara nästa student.
    </div>
  </div>

  <!-- NEW: Personligt avsnitt -->
  <div class="omStory omCard">
    <h2>Hur allt började</h2>
    <p>
      AlltFörStudenten.se startade inför studenten 2011.
    </p>
    <p>
      Bakgrunden var enkel: det fanns ingen samlad plats som berättade om <em>allt</em> kring studenten.
      Vissa sidor fokuserade på studentflak. Andra på studentfester eller balen.
      Men helheten saknades.
    </p>
    <p>
      Vi ville skapa en plattform där hela studentfirandet fick plats – från bal och kläder till flak,
      mottagning, presenter och lokala företag. En plats där både elever och föräldrar enkelt kunde hitta
      inspiration, struktur och konkreta tips.
    </p>
    <p>
      Sedan starten har <strong>över 800 000 studenter och deras familjer</strong> besökt oss för att planera och inspireras
      inför en av livets största milstolpar. Varje år fortsätter nya årskullar att hitta hit – med samma frågor,
      samma förväntningar och samma pirr i magen.
    </p>
    <p>
      Bakom sidan finns ett litet, engagerat team som brinner för tydlighet, kvalitet och långsiktighet.
      Vi utvecklar plattformen steg för steg – med målet att göra studentplaneringen enklare, mer överskådlig
      och mer lokal.
    </p>
    <p>
      Har du frågor, vill synas inför studenten eller sitter på ett tips som kan hjälpa nästa årskull?
      Tveka inte att höra av dig. Vi lyssnar.
    </p>
  </div>

  <div class="omCta">
    <h2>Vill du synas inför studenten?</h2>
    <p>
      Är du företagare och vill nå rätt målgrupp inför studenten?
      Kontakta oss så berättar vi mer om hur en placering fungerar.
    </p>

    <a class="omBtn" href="<?php echo base_url(); ?>kontakt">
      Kontakta oss →
    </a>
  </div>

</div>

<?php $this->load->view('_footer'); ?>