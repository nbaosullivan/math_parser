import React from "react";
import ReactDOM from "react-dom";
import MathFormulaList from "./components/MathFormulaList"

function createFormulaList(nid) {
    let list = [];
    let formulas = document.querySelectorAll('[id^="math-parser-item-"');
    for (var i = 0; i < formulas.length; i++) {
        let math_parser_template_item = document.getElementById('math-parser-item-' + i);
        list.push({
            formula: math_parser_template_item.getAttribute("data-formula"),
            result: math_parser_template_item.getAttribute("data-result"),
            delta: math_parser_template_item.getAttribute("data-delta"),
            nid: nid,
        });
    }
    return list

}

let formula_list = document.querySelectorAll('[id^="math_parser_list_"');
for (var i = 0; i < formula_list.length; i++) {
    ReactDOM.render(
        <MathFormulaList children={createFormulaList(formula_list[i].getAttribute('data-nid'))}/>,
        document.getElementById(formula_list[i].id));
}

