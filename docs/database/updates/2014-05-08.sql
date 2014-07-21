-- From lool0

CREATE INDEX idx_acos_lft_rght ON `acos`(lft,rght);
CREATE INDEX idx_acos_alias ON `acos`(alias);
CREATE INDEX idx_acos_model_foreign_key ON `acos`(model(255),foreign_key);
CREATE INDEX idx_aros_lft_rght ON `aros`(lft,rght);
CREATE INDEX idx_aros_alias ON `aros`(alias);
CREATE INDEX idx_aros_model_foreign_key ON `aros`(model(255),foreign_key);
CREATE UNIQUE INDEX idx_aros_acos_aro_id_aco_id ON `aros_acos`(aro_id, aco_id);
ALTER TABLE aros_acos ADD CONSTRAINT FOREIGN KEY (aro_id) REFERENCES `aros`(id);
ALTER TABLE aros_acos ADD CONSTRAINT FOREIGN KEY (aco_id) REFERENCES `acos`(id);