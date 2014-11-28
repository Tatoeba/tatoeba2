#!/bin/bash
sed 's/^#: /#: github.com\/Tatoeba\/tatoeba2\/tree\/dev\/app/g' -i *.pot
sed 's/:\([0-9]\+\)$/#L\1/' -i *.pot
