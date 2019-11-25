import React, { forwardRef } from 'react';
import { Select } from 'antd';
import Drpdown from './../services/drpdown.js';
import { isEmpty, splitStrToArrInt } from './utils';

const categories = Drpdown.getActiveCategories();
const Category = (props, ref) => {
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

const WrappedCategory = forwardRef(Category);
export { WrappedCategory as SelectCategory };






