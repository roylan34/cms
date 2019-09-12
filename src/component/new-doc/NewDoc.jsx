import React from 'react';
import CardGrid from './CardGrid';
import './new.css';


const cardName = [
    { title: "Printer Management Agreement", description: "Updated at 08/23/2019" },
    { title: "Annex-A", description: "Updated at 08/23/2019" },
    { title: "Annex-B", description: "Updated at 08/23/2019" },
    { title: "Annex-C", description: "Updated at 08/23/2019" },
    { title: "Annex-D", description: "Updated at 08/23/2019" }
];

export default function NewDoc() {

    const cards = cardName.map(card => {
        return (
            <CardGrid
                key={card.title}
                title={card.title}
                description={card.description}
            />
        )
    });

    return (

        <div >
            <div className="text-center new-doc-header">
                <h4>Create new contract</h4>
            </div>
            <div className="col-md-12 new-doc-subheader ">
                <h5>Contract templates</h5>
            </div>
            <div className="col-md-12 card-templates">
                {cards}
            </div>
        </div >

    )

}