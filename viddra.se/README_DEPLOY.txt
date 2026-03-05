VIDDRA — Step 36 Forecast (run-rate projection)
Generated: 2026-02-27

NEW
- /core/Forecast.php
- /app/forecast.php

HOW IT WORKS
- For current month:
  projected = (actual_to_date / days_elapsed) * days_in_month
- For past months:
  projected = actual (full month)
- Compares projected vs budget per category + totals

DEPENDS ON
- viddra_transactions
- viddra_categories
- viddra_budgets
