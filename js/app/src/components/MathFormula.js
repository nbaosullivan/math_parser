import React, {Component} from 'react';
import {ApolloClient} from 'apollo-client';
import {HttpLink} from 'apollo-link-http';
import {InMemoryCache} from 'apollo-cache-inmemory';
import gql from "graphql-tag";

const client = new ApolloClient({
    // By default, this client will send queries to the
    //  `/graphql` endpoint on the same host
    // Pass the configuration option { uri: YOUR_GRAPHQL_API_URL } to the `HttpLink` to connect
    // to a different host
    link: new HttpLink(),
    cache: new InMemoryCache(),
});

class MathFormula extends Component {

    constructor(props) {
        super(props);
        this.state = {
            maxTries:3,
            status: 0,
            userAnswer: '',
            formulaAnswer: '?',
            delta: props.delta,
            formula: props.formula,
            nid: props.nid,
            tries: 0,
            outcome: ''
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleKeyDown = this.handleKeyDown.bind(this);

    }
    handleChange() {
        this.setState({userAnswer: event.target.value});
    }

    handleKeyDown(e) {
        if (e.key === 'Enter') {

            // Use GraphQL see what the real answer is
            client.query({
                query: gql`
                    query answerQuery($nid: String!, $delta: String!){
                        mathAnswer(filter: {nid:{value: $nid}, delta: {value: $delta}}) {
                          ...on MathAnswerResult {
                            results{
                              answer
                            }
                          }
                      }
                    }
                `,
                variables: { nid: this.state.nid, delta: this.state.delta}
            }).then(response => {
                let answer = response.data.mathAnswer.results[0].answer;

                if(answer == this.state.userAnswer){
                    this.setState({outcome: 'CORRECT!'})
                    // Increment parent count for results
                    this.props.next()
                    this.props.increment()
                } else {
                    if(this.state.tries < this.state.maxTries){
                        this.setState({tries : this.state.tries + 1})
                    }
                    // Three attempts then move on
                    if(this.state.tries >= this.state.maxTries){
                        this.setState({outcome: 'INCORRECT. The answer is: ' + answer})
                        this.props.next()
                    } else {

                    }
                }
            }
            )
        }
    }

    render() {
        return (
            <div className="formula-item">
                <h2>Question {parseInt(this.state.delta) + 1}</h2>
                <h1 className={'formula-' + this.state.delta}>
                    <span onMouseEnter={this.showContent} className="formula-sum">{this.state.formula}</span>
                    <span className="formula-equals"> = </span>
                    <input autoFocus type="number" id="answer" value={this.state.userAnswer} onKeyDown={this.handleKeyDown}
                           onChange={this.handleChange}/>
                </h1>
                <small className={((this.state.maxTries - this.state.tries) <= 1) ? 'danger': 'success'}>Attempts left: {this.state.maxTries - this.state.tries}</small>
                {this.state.outcome &&
                <p className={(this.state.outcome == 'CORRECT!' ? 'success' : 'danger')}><strong>{this.state.outcome}</strong></p>
                }
            </div>
        );
    }


}

export default MathFormula;