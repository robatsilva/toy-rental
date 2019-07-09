INSERT INTO kiosks (kiosks.NAME, user_id, kiosks.`default`, kiosks.tolerance, kiosks.extra_value, kiosks.`status`) 
VALUES ('Nome Kiosk', 6, 0, 2, 3, 1);

INSERT INTO kiosk_user VALUES (33, 6, 0);
INSERT INTO kiosk_user VALUES (33, 8, 0);
INSERT INTO kiosk_user VALUES (33, 30, 0);

INSERT INTO cash_drawers (cash_drawers.name, cash_drawers.kiosk_id, STATUS)
VALUES ('Caixa 1', 33, 1);