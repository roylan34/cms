import React, { useEffect, useRef, Fragment } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { addToner, removeToner, addTonerPrice, removeTonerPrice } from './../../actions/pmaAction';

import { Form, Input, InputNumber, DatePicker, Button } from 'antd';
import './sheets-of-pma.less';
import FixedHeader from './FixedHeader';

const { MonthPicker } = DatePicker;


function Pma() {
    const tonerFields = useSelector(state => state.pmaReducer.addToner);
    const dispatch = useDispatch();

    useEffect(() => {
        console.log(tonerFields);
    });

    function handleChangeInitial(idx, e) {
        dispatch({ type: "INPUT_TONER_INITIAL", payload: { idx: idx, name: e.target.name, value: e.target.value } });
    }
    function handleChangePrice(idxInitial, idxPrice, e) {
        dispatch({ type: "INPUT_TONER_PRICE", payload: { idxInitial: idxInitial, idxPrice: idxPrice, name: e.target.name, value: e.target.value } });
    }
    function ListTonerDescription() {
        return (
            <Fragment>
                <ul className="pma-toner-description">
                    <li>Laser Printer units more particularly described in Annex A, free of charge provided the conditions set forth below are met.</li>
                    {
                        tonerFields.map((field, idx) => {
                            return (
                                < li key={idx}>
                                    An initial order of
                                    <Input style={{ width: '15%' }} size="small" name="initial" onChange={(e) => handleChangeInitial(idx, e)} defaultValue={field.initial} />
                                    {/* <input style={{ width: '15%' }} className="ant-input ant-input-sm" name="initial" onChange={(e) => handleChangeDscrp(idx, e)} value={field.initial} /> */}
                                    toner cartridges is required per machine unit with minimum of
                                <Input style={{ width: '15%' }} size="small" name="min" onChange={(e) => handleChangeInitial(idx, e)} defaultValue={field.min} /> toner cartridges per year for monochrome printers. The amount of each toner cartridge is as follows;
                                <Button size="small" type="danger" title="Remove Initial" onClick={() => dispatch(removeToner(idx))}>-</Button>
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
                                <Input placeholder="Toner Model" size="small" name="model" onChange={(e) => handleChangePrice(index, idx, e)} defaultValue={field.model} />
                                <span>--</span> <Input placeholder="Toner Price" size="small" name="price" onChange={(e) => handleChangePrice(index, idx, e)} defaultValue={field.price} />
                                <span><Button size="small" type="danger" title="Remove Price" onClick={() => dispatch(removeTonerPrice(index, idx))}>-</Button></span>
                            </div>
                        </li>)
                    })
                }
                <li><Button size="small" type="primary" onClick={() => dispatch(addTonerPrice(index))}>+ Add Price</Button></li>
            </ul>
        )
    }

    function PmaTemplate() {

        return (
            <div className="template-container">
                {/* Custom HTML header  */}
                <div id="page-border">
                    <div id="page-outline"></div>
                </div>
                <div className="page page-size-legal">
                    <Form action="">
                        <div className="pma-container">
                            <div className="pma-details">
                                <p>KNOW ALL MEN BY THESE PRESENTS:</p>
                                <div className="p-text-indent">This Agreement made and entered into this <span><InputNumber placeholder="Day" name="" size="small" /> day of
                        <MonthPicker format="MM-YYYY" placeholder="MM-YYYY" style={{ width: '18%' }} size="small" /></span>, in the City of Makati by and between:</div>
                                <p className="p-text-indent">DELSAN OFFICE SYSTEMS CORPORATION, with business address at 7893 Lawaan Street, San Antonio Village, Makati City, Philippines,
                    represented herein by its President & CEO MR. ODILON A. SANTOS, hereinafter referred to as the “OWNER”;</p>
                                <p className="text-center">WITNESSETH</p>
                                <p>WHEREAS, <Input placeholder="Company Name" style={{ width: '50%' }} size="small" /> agree to enter into Printer Management Agreement - Toner Program.</p>
                                <p>NOW THEREFORE, for and in consideration of the foregoing premises, the parties hereto hereby agree as follows:</p>
                                <div className="pma-consumable-purchase">
                                    <div>1. Consumable Purchase Commitment</div>
                                    <div className="parags">
                                        <p className="parag-a">a. Upon the effective date hereof, OWNER will supply to USER:
                                <ListTonerDescription />
                                            <div><Button size="small" type="primary" onClick={() => dispatch(addToner())}>+ Add Initial</Button></div>
                                        </p>
                                        <p>b. THEREAFTER, all <strong>Toner Cartridges</strong> to be used on the above printers are to be supplied solely by OWNER. USER will notify OWNER when cartridges are empty and should issue corresponding replenishment through USER purchase order.
                                            OWNER or its authorized representative has exclusive rights to collect and recover empty cartridges through agreed schedule/s. USER must exercise due care in the storage of new and empty toner cartridges, and shall not dispose,
                                    sell or assign the Owner’s empty toner cartridges. Any loss and/or damage of empty toner cartridge(s) shall be for the account of USER which is equivalent to 20% of cost of toner. </p>
                                        <p>c. This agreement shall not include paper supply.</p>
                                        <p>d. The contract price per unit of DELSAN supplied consumables and its price validity period is as listed in “Annex A”. Other than what is provided in Annex A, there shall be no other charges, fees or expenses.</p>
                                        <p>e. Unless otherwise agreed upon, all goods and services shall be provided by the OWNER, which contract prices are as provided in Annex A, delivery fees and charges up to the designated point of delivery indicated in the purchase order are included in the contract price.</p>
                                        <p className="parag-f">f. These contract prices are based on a foreign exchange (FOREX) reference rate indicated in “Annex A”. Should the Philippine Peso depreciate or appreciate against the US dollar beyond <Input size="small" placeholder="Ex: six percent (± 6%)" /> and the depreciation or appreciation has taken effect for more than two calendar weeks, the contract prices indicated in “Annex A” shall be adjusted up to the extent proportionate to the actual depreciation or appreciation.</p>
                                        <p>g. The OWNER service and maintenance services are valid and applicable for printers enrolled in this agreement for the duration of the price validity period indicated in “Annex A” and for the duration of any renewal or extension thereto.</p>
                                    </div>
                                </div>
                                <div className="parags">
                                    <p>h. Both parties may, from time to time, agree to add items or remove items from “Annex A” by executing the form known as “Annex B” which is supplemental to this agreement. For additional units, USER should comply with the required commitment indicated on Section 1. B and/or C. Contract term period on additional unit(s) should be excluded from the original period coverage. Thus, commencement of such will be from the date of installation and will expire on the minimum required period specified on Section 7.</p>
                                    <p>i. Included in the price of the toner cartridge is the free use of laser printer/s, unlimited on-site emergency service and scheduled maintenance.</p>
                                </div>
                                <div className="pma-purchase-process">
                                    <div>2. Purchase Process</div>
                                    <div className="parags">
                                        <p>a. The USER shall issue all purchase orders specified in Section 1. Consumable Purchase Commitment solely to the OWNER.</p>
                                        <p>b. Upon receipt of purchase order, the OWNER shall deliver ordered laser printer(s) and/or toner within three (3) to five (5) working days to USER. If ordered laser printer and/or toner is not available, the OWNER shall inform the USER immediately of the earliest possible delivery date.</p>
                                    </div>
                                </div>

                                <div className="pma-payments">
                                    <div>3.	Payment of Accounts</div>
                                    <div className="parags">
                                        <p>a. All accounts shall be payable to the OWNER within thirty (30) days from the date of sales invoice.</p>
                                        <p>b. Should USER fail to issue payment to OWNER without offering any reasonable explanation, the OWNER shall be given the option to hold delivery of new purchase order, hold commitment on service level agreement, terminate this agreement and/or remove the laser printer unit/s being used for this Printer Management Agreement until such overdue account is paid by the USER. User will be held liable for 3% daily penalty charges of total outstanding receivable balance calculated.</p>
                                        <p>c. The printer shall remain the property of the OWNER. Laser printer shall be installed only at USER office premise/s and shall not be transferred to another location without prior notice to the OWNER. Any loss of printer/s and its supplies is held liable on the USER using the depreciated value of such item/s.</p>
                                        <p>d. During the term of the agreement, while the laser printers are in the possession of the USER, the latter shall:
                                    <ul>
                                                <li>d.1. Use only DELSAN toner cartridges for the laser printers unit(s). Any damage caused on the machine as a result or in connection with the use of unauthorized consumables shall be for the account of the USER.</li>
                                                <li>d.2. Unauthorized persons shall not be allowed to operate the laser printers. Any damage caused by improper operation of the laser printers shall likewise be for the account of the USER.</li>
                                                <li>d.3. The USER shall not be held responsible for any loss and/or damage to the laser printer due to any act of God, fire, casualty, flood, war strike, lock out failure of public utilities, injunction or any act exercise, assertion or requirement of governmental authority, epidemic, destruction of facilities, insurrection, labor, equipment, transportation or any other cause beyond the reasonable control of the USER.</li>
                                                <li>d.4. The USER shall not make any alterations on the laser printers, nor sub-lease, pledge, mortgage or exercise any act of ownership over said laser printers. Further, the rights of the USER under this agreement cannot be assigned without the written consent of the OWNER.</li>
                                            </ul>
                                        </p>
                                        <p></p>
                                        <p></p>
                                    </div>
                                </div>
                                <div className="pma-service-support">
                                    <div>4. Service Support</div>
                                    <div className="parags">
                                        <p>a. Should the laser printer encounter a mechanical problem or would be needing technical support, the OWNER shall repair the printer within 24 hours from notice (oral or written) or may issue a service unit.</p>
                                        <p>b. To prevent disruption in the operations of the USER, OWNER shall immediately replace defective or damaged unit without fault on the part of the USER.</p>
                                        <p>c. Preventive Maintenance (PM) and Repair Maintenance (RM) services are available:
                                    <ul>
                                                <li>Monday to Saturday
                                            <div> Helpdesk – 7:00am to 7:00pm</div>
                                                    <div>Field Technicians – 8:00am to 5:00pm</div>
                                                </li>
                                                <li>Calls made after 3:00 p.m. shall be served in the next working day.</li>
                                            </ul>
                                        </p>
                                    </div>
                                </div>
                                <p>5. If any condition or provision of this Agreement is held invalid or declared contrary to law, the validity of the other conditions or provision shall not be affected.</p>
                                <p>6. Legal actions arising out of this contract shall be brought in/or submitted to the jurisdiction of the proper court of the Municipality of Makati at the option of the OWNER.</p>
                                <p>7. The Contract shall be for a period of <Input placeholder="Contract Terms" style={{ width: '40%' }} size="small" /> years and may be renewed upon mutual agreement of both parties. The OWNER may revoke the contract if the USER violate any terms, conditions and/or other fraudulent act/s specified on the contract agreement. On the other hand, the USER may revoke the contract thru written notice of at least thirty (30) days before such termination with the following conditions;</p>
                            </div>
                            <div className="pma-sign">
                                <p className="pma-witnesses-date">IN WITNESS WHEREOF, the PARTIES, through their representatives duly authorized for the purpose, have hereunto set their hands this <Input size="small" /> day of <Input size="small" /> at the City of Makati, Philippines.</p>
                                <div className="pma-signatory">
                                    <div>
                                        <div>Company Name</div>
                                        <div>Signature</div>
                                        <div >
                                            <Input size="small" style={{ width: "60%" }} />
                                            <div>Designation</div>
                                        </div>
                                    </div>
                                    <div>
                                        <div>Delsa Company</div>
                                        <div>Signature</div>
                                        <div className="signature">
                                            <Button type="ghost">SIGN</Button>
                                        </div>
                                        <div >
                                            <div>ODILON A. SANTOS</div>
                                            <div>President & CEO</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="pma-witness">
                                <p className="text-center">WITNESSES</p>
                                <div className="pma-signatory">
                                    <div className="witness-one">
                                        <Input size="small" style={{ width: "60%" }} />
                                    </div>
                                    <div className="witness-two">
                                        <div className="signature">
                                            <Button type="ghost">SIGN</Button>
                                        </div>
                                        <div >
                                            <div>TOYNBEE G. NAVARRO</div>
                                            <div>General Manager - VizMin</div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div>REPUBLIC OF THE PHILIPPINES </div>
                                    <div className="col-md-1 remove-col-padlr">CITY OF</div>
                                    <div className="col-md-2 col-xs-6 remove-col-padlr"> <Input size="small" /></div>) S.S.
                            </div>
                                <p>BEFORE ME, a Notary Public, this _______ day of _______ 2019 personally came and appeared the following:</p>
                                <div>
                                    <div className="col-md-4 col-sm-6 ">
                                        <ul className="list-inline">
                                            <li>USER SIGNATORY</li>
                                            <li>CTC No. ____________</li>
                                            <li>Issued at ____________</li>
                                            <li>Issued on ____________</li>
                                        </ul>
                                    </div>
                                    <div className="col-md-4 col-sm-6 ">
                                        <ul className="list-inline">
                                            <li>USER SIGNATORY</li>
                                            <li>CTC No. ____________</li>
                                            <li>Issued at ____________</li>
                                            <li>Issued on ____________</li>
                                        </ul>
                                    </div>
                                    <div className="col-md-4 col-sm-6 ">
                                        <ul className="list-inline">
                                            <li>ODILON A. SANTOS</li>
                                            <li>Passport No. P7629261A</li>
                                            <li>Issued at DFA NCR SOUTH</li>
                                            <li>Issued on JUNE 21, 2018</li>
                                        </ul>
                                    </div>
                                </div>
                                <p>All known to me to be the same persons who executed the foregoing instrument and acknowledged to me that the same are their free and voluntary act as well as the free and voluntary act and deed of the entitles they present in this instance.</p>
                                <p>This instrument consisting of three (3) pages, including the page on which this acknowledgement is written has been signed on the left margin of each and every page thereof by the parties and their instrumental witnesses and sealed with my notarial will.</p>
                                <p>WITNESS MY HAND AND SEAL this _____ day of _____ 2019 at the City of Makati, Philippines</p>
                                <p>Notary Public:
                                <ul className="list-unstyled">
                                        <li>DOC. No.:_______;</li>
                                        <li>Page No.:________;</li>
                                        <li>Book No.:________;</li>
                                        <li>Series of {new Date().getFullYear()}</li>
                                    </ul>
                                </p>
                            </div>

                        </div>
                    </Form>
                </div >
                {/* <div className="">
                <div className="pma-template">
                    <div>

                    </div>
                    <div>

                    </div>
                </div>
            </div> */}
            </div>
        );
    }

    return (
        <PmaTemplate />

    )
}

export default function PmaTemplate() {
    return (
        <Fragment>
            <FixedHeader filename="pma" />
            <Pma />
        </Fragment>
    )

}