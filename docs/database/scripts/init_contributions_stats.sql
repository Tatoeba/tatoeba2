INSERT INTO contributions_stats(`date`, `lang`, `sentences`, `action`, `type`)
  SELECT date_format(`datetime`, "%Y-%m-%d") as `day`, NULL, COUNT(*), `action`, `type`
  FROM contributions
  WHERE type = 'sentence' AND action = 'insert'
  GROUP BY `day`;