import React, { Component } from 'react';

class MathFormulaResult extends Component{
    constructor() {
        super();
    }
    render(){
        return (
            <div>
                <h2>Your result</h2>
                <p>You got {this.props.correctAnswers}/{this.props.totalFormulas} correct.</p>
            </div>
        )
    }
}
export default MathFormulaResult;