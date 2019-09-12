import React from 'react';
import { Card } from 'antd';
import { ReactComponent as Contractsvg } from './../../assets/img/contract.svg';


export default function CardGrid(props) {

    return (
        <div className="">
            <Card
                size="small"
                hoverable
                bordered={false}
                style={{ textAlign: "center", }}
                cover={<Contractsvg width="4em" height="4em" style={{ opacity: 0.7, padding: "10 0" }} />}
            >
                <Card.Meta title={props.title} description={props.description} />
            </Card>
        </div>

    );

}