import React, { Fragment } from 'react';
import { useSelector } from 'react-redux';
import './pma-pdf-style.less';

export default function PmaPDF() {
    const tonerFields = useSelector(state => state.pmaReducer.addToner);

    function ListTonerDescription() {
        return (
            <Fragment>
                <ul className="pma-toner-description">
                    <li>Laser Printer units more particularly described in Annex A, free of charge provided the conditions set forth below are met.</li>
                    {
                        tonerFields.map((field, idx) => {
                            return (
                                < li key={idx}>
                                    An initial order of {field.initial}
                                    toner cartridges is required per machine unit with minimum of {field.min} toner cartridges per year for monochrome printers.
                                    The amount of each toner cartridge is as follows;
                                    <ListTonerPrice index={idx} />
                                </li>)
                        })
                    }
                </ul >
            </Fragment>
        )
    }
    function ListTonerPrice({ index }) {
        return (
            <ul className="pma-toner-price">
                {
                    tonerFields[index].price.map((field, idx) => {
                        return (<li key={idx}>
                            <div>
                                {field.model} <span>--</span> {field.price}
                            </div>
                        </li>)
                    })
                }
            </ul>
        )
    }

    function PmaTemplate() {

        return (
            <div>
                <div className="export-pdf">
                    <h4>Hallowwwwwwwwwwwww</h4>
                </div>
            </div>
        );
    }

    return (
        <PmaTemplate />

    )
}
