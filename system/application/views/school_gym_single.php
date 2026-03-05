<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>

    <link rel="canonical" href="<?php echo htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="robots" content="index,follow">

    <?php
    // Meta description: kort, saklig, utan att lova datum om vi inte visar verifierad källa här
    $name = !empty($school['namn']) ? $school['namn'] : 'Skola';
    $kommun = !empty($kommun_name) ? $kommun_name : (!empty($school['kommun_namn']) ? $school['kommun_namn'] : (!empty($school['locality']) ? $school['locality'] : ''));
    $desc = $name;
    if ($kommun) $desc .= ' i ' . $kommun;
    $desc .= '. Skolenhetskod, kontakt och länkar för studentplanering.';
    ?>
    <meta name="description" content="<?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'); ?>">

    <!-- Basic Open Graph (snällt och ofarligt) -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:type" content="website">

    <?php
    // Breadcrumbs (JSON-LD)
    $items = array();
    $items[] = array('name' => 'AlltFörStudenten', 'item' => rtrim($canonical, '/'));
    // Vi bygger en korrekt breadcrumb-kedja om vi har län/kommun-länkar
    $base = preg_replace('#/skola/.*$#', '', $canonical);
    $items = array();
    $items[] = array('name' => 'AlltFörStudenten', 'item' => $base . '/');
    if (!empty($lan_url) && !empty($lan_name)) $items[] = array('name' => $lan_name, 'item' => $lan_url);
    else if (!empty($lan_url)) $items[] = array('name' => 'Län', 'item' => $lan_url);
    if (!empty($kommun_url) && $kommun) $items[] = array('name' => $kommun, 'item' => $kommun_url);
    $items[] = array('name' => $name, 'item' => $canonical);

    $breadcrumb = array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array()
    );
    for ($i = 0; $i < count($items); $i++) {
        $breadcrumb['itemListElement'][] = array(
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $items[$i]['name'],
            'item' => $items[$i]['item']
        );
    }

    // FAQ (JSON-LD) – generiska svar som alltid stämmer
    $faq = array(
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array(
            array(
                '@type' => 'Question',
                'name' => 'Hur hittar jag studentdatum för ' . $name . ' 2026?',
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => 'Studentdatum kan skilja sig mellan skolor. Om AlltFörStudenten visar verifierade datum för skolan så anges alltid källa. Annars hittar du datum på skolans officiella webbplats eller via kommunen.'
                )
            ),
            array(
                '@type' => 'Question',
                'name' => 'Varför finns det olika datum för bal och student?',
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => 'Student (utspring) och bal är ofta separata arrangemang och kan ligga på olika dagar. Därför redovisas de som två olika datum när informationen finns.'
                )
            ),
            array(
                '@type' => 'Question',
                'name' => 'Vad betyder skolenhetskod?',
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => 'Skolenhetskod är en unik kod som identifierar en skolenhet i Skolverkets register. Den gör det enklare att hitta rätt skola även om namnet ändras över tid.'
                )
            ),
        )
    );

    // EducationalOrganization (lätt och säker markup)
    $org = array(
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => $name,
        'url' => $canonical
    );
    if (!empty($school['url'])) $org['sameAs'] = $school['url'];
    ?>
    <script type="application/ld+json"><?php echo json_encode($breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
    <script type="application/ld+json"><?php echo json_encode($faq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
    <script type="application/ld+json"><?php echo json_encode($org, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
</head>
<body>

    <nav aria-label="Brödsmulor">
        <p style="margin:0 0 10px 0;">
            <a href="<?php echo htmlspecialchars($base . '/', ENT_QUOTES, 'UTF-8'); ?>">AlltFörStudenten</a>
            <?php if (!empty($lan_url)): ?>
                &nbsp;›&nbsp;<a href="<?php echo htmlspecialchars($lan_url, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(!empty($lan_name) ? $lan_name : 'Län', ENT_QUOTES, 'UTF-8'); ?></a>
            <?php endif; ?>
            <?php if (!empty($kommun_url) && $kommun): ?>
                &nbsp;›&nbsp;<a href="<?php echo htmlspecialchars($kommun_url, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($kommun, ENT_QUOTES, 'UTF-8'); ?></a>
            <?php endif; ?>
            &nbsp;›&nbsp;<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
        </p>
    </nav>

    <h1><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></h1>


    <?php if (!empty($unit_links)): ?>
        <div style="margin:14px 0 18px 0; padding:12px 14px; border:1px solid #e7e9ef; border-radius:12px; background:#f7f8fa;">
            <strong>Fler enheter:</strong>
            <span style="color:#556;">
                <?php foreach($unit_links as $u): ?>
                    <a href="<?php echo htmlspecialchars($u['url'], ENT_QUOTES, 'UTF-8'); ?>" style="margin-right:10px;">
                        <?php echo htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php endforeach; ?>
            </span>
        </div>
    <?php endif; ?>


    <?php if ($kommun): ?>
        <p>
            <strong>Ort/kommun:</strong>
            <?php if (!empty($kommun_url)): ?>
                <a href="<?php echo htmlspecialchars($kommun_url, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($kommun, ENT_QUOTES, 'UTF-8'); ?></a>
            <?php else: ?>
                <?php echo htmlspecialchars($kommun, ENT_QUOTES, 'UTF-8'); ?>
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <h2>Kontakt och webb</h2>
    <p>
        <?php if (!empty($school['street_address'])): ?>
            <?php echo htmlspecialchars($school['street_address'], ENT_QUOTES, 'UTF-8'); ?><br>
        <?php endif; ?>

        <?php if (!empty($school['postal_code']) || !empty($school['locality'])): ?>
            <?php echo htmlspecialchars(!empty($school['postal_code']) ? $school['postal_code'] : '', ENT_QUOTES, 'UTF-8'); ?>
            <?php echo htmlspecialchars(!empty($school['locality']) ? $school['locality'] : '', ENT_QUOTES, 'UTF-8'); ?>
            <br>
        <?php endif; ?>

        <?php if (!empty($school['email'])): ?>
            E-post: <a href="mailto:<?php echo htmlspecialchars($school['email'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($school['email'], ENT_QUOTES, 'UTF-8'); ?></a><br>
        <?php endif; ?>

        <?php if (!empty($school['url'])): ?>
            Webb: <a href="<?php echo htmlspecialchars($school['url'], ENT_QUOTES, 'UTF-8'); ?>" rel="nofollow noopener" target="_blank"><?php echo htmlspecialchars($school['url'], ENT_QUOTES, 'UTF-8'); ?></a><br>
        <?php endif; ?>
    </p>

    <h2>Skolinfo</h2>
    <p>
        <strong>Skolenhetskod:</strong> <?php echo htmlspecialchars(!empty($school['skolenhetskod']) ? $school['skolenhetskod'] : '', ENT_QUOTES, 'UTF-8'); ?><br>
        <?php if (isset($school['status'])): ?>
            <strong>Status:</strong> <?php echo htmlspecialchars($school['status'], ENT_QUOTES, 'UTF-8'); ?><br>
        <?php endif; ?>
    </p>

    <?php if (!empty($kommun_url)): ?>
        <h2>Studenten i kommunen</h2>
        <p>
            Planerar du studentbal, utspring och mottagning? Gå till kommunens checklista här:
            <a href="<?php echo htmlspecialchars($kommun_url, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($kommun ? $kommun : 'Kommun', ENT_QUOTES, 'UTF-8'); ?></a>.
        </p>
    <?php endif; ?>

    <h2>Vanliga frågor</h2>
    <p><strong>Hur hittar jag studentdatum?</strong><br>Om AlltFörStudenten visar verifierade datum för skolan så anges alltid källa. Annars: kolla skolans officiella webbplats.</p>
    <p><strong>Varför kan bal- och studentdatum vara olika?</strong><br>Bal och student är ofta separata arrangemang och kan därför ligga på olika dagar.</p>
    <p><strong>Vad är skolenhetskod?</strong><br>En unik kod i Skolverkets register som hjälper dig hitta rätt skola även om namnet ändras.</p>

</body>
</html>
