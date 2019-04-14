import React, { Component } from 'react';
import MathFormula from './MathFormula';
import MathFormulaResult from './MathFormulaResult';

class MathFormulaList extends Component {
    constructor(props) {
        super(props);
        this.state = {
            currentFormula: 0,
            totalFormulas: this.props.children.length,
            correctAnswers: 0,
            complete: false,
            result: []
        };
    }

    nextFormula() {
        let nextDelta = this.state.currentFormula + 1;
        if (nextDelta < this.state.totalFormulas) {
            this.setState({
                currentFormula: nextDelta
            })
        } else {
            this.setState({
                complete: true
            })
        }
    }

    incrementAnswers() {
        this.setState({
            correctAnswers: this.state.correctAnswers + 1
        })
    }

    render() {
        return (
            <div>
                <div className={'formula-list'}>
                    {this.props.children.map((item, key) => {
                            if (item.delta <= this.state.currentFormula) {
                                return <MathFormula
                                    key={key}
                                    formula={item.formula}
                                    delta={item.delta}
                                    nid={item.nid}
                                    next={this.nextFormula.bind(this)}
                                    increment={this.incrementAnswers.bind(this)}
                                />
                            }
                        }
                    )}
                </div>
                {this.state.complete &&
                    <MathFormulaResult totalFormulas={this.state.totalFormulas} correctAnswers={this.state.correctAnswers}/>
                }
            </div>
        );
    }
}
export default MathFormulaList;