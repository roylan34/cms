import React, { forwardRef } from 'react';
import { Select } from 'antd';
import Drpdown from './../services/drpdown.js';


const categories = Drpdown.getActiveCategories();
function Category(props, ref) {
    const opt_cat = categories.aaData.map(opt =>
        <Select.Option key={opt.id} value={opt.id}>{opt.cat_name}</Select.Option>
    )
    return (
        <Select
            {...props}
            allowClear={true}
            style={{ width: '100%' }}
            ref={ref}
        >
            {opt_cat}
        </Select>
    )
}

export const SelectCategory = forwardRef(Category);







