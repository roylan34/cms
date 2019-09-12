import React from 'react';
import Select from 'react-select';
import AsyncSelect from 'react-select/async';
import BranchServices from '../services/branchServices';
import AccountServices from '../services/accountServices';
import BrandServices from '../services/brandServices';
import SapServices from '../services/sapServices';
import { isEmpty, splitStrToArrInt } from './utils';

const branchOptions = BranchServices.getBranches();
let brandsOptions = BrandServices.getBrand();
brandsOptions = [{ id: 0, brand_name: '--Brand--' }, ...brandsOptions];

export const SelectBranch = (props) => {
	//Get the object values.
	let getValue = (opts, val) => (val !== '' ? opts.find(o => o.id === val) : null);

	//Remove branch.
	let excludeBranch = (opts) => {

		if (props.isExclude) {
			//Split string to array.
			let branches = (!isEmpty(props.excludeBranch) ? props.excludeBranch.split(",") : null);
			if (props.excludeBranch == 1) { //if Location is 1 = ALL, means display all locations.
				return opts.filter(elem => !branches.includes(elem.id));
			} else {
				return opts.filter(elem => branches.includes(elem.id));
			}
		} else {
			return branchOptions;
		}
	}
	return (
		<Select
			{...props}
			value={getValue(branchOptions, props.val)}
			isClearable={true}
			getOptionValue={(data) => data.id}
			getOptionLabel={(data) => data.branch_name}
			options={excludeBranch(branchOptions)}
		/>
	)
}

export const SelectBranchMulti = (props) => {
	//Get the object values.
	let value = props.val.split(",") || '';
	const getValue = (opts, val) => (val !== '' ? opts.filter(elem => val.includes(elem.id)) : null);

	//Remove branch.
	let excludeBranch = (opts) => {
		if (props.isExclude) {
			//Split string to array.
			let branches = (!isEmpty(props.excludeBranch) ? props.excludeBranch.split(",") : null);
			if (props.excludeBranch == 1) { //if Location is 1 = ALL, means display all locations.
				return opts.filter(elem => !branches.includes(elem.id));
			} else {
				return opts.filter(elem => branches.includes(elem.id));
			}
		} else {
			return branchOptions;
		}
	}
	return (
		<Select
			{...props}
			value={getValue(branchOptions, value)}
			isMulti={true}
			isClearable={true}
			getOptionValue={(data) => data.id}
			getOptionLabel={(data) => data.branch_name}
			options={excludeBranch(branchOptions)}
		/>
	)
}
const acctMngrOptions = AccountServices.getAccountMngr();
export const SelectAcctMngr = (props) => {
	//Get the object values.
	const getValue = (opts, val) => (val !== '' ? opts.find(o => o.id === val) : null);

	return (
		<Select
			{...props}
			value={getValue(acctMngrOptions, props.val)}
			isClearable={true}
			getOptionValue={(data) => data.id}
			getOptionLabel={(data) => data.fullname}
			options={acctMngrOptions}
		/>
	)
}


export const SelectSapComp = (props) => {
	let formattedOpt = (input) => {
		return SapServices.getCompByName(input).map(sap => {
			var obj = {};
			obj["value"] = sap.sap_code;
			obj["label"] = sap.company_name;
			return obj;
		});
	}

	let filterVal = (inputValue) =>
		formattedOpt(inputValue).filter(i =>
			i.label.toLowerCase().includes(inputValue.toLowerCase())
		);

	let sapOptions = inputValue =>
		new Promise(resolve => {
			setTimeout(() => {
				resolve(filterVal(inputValue));
			}, 1000);
		});

	return (
		<AsyncSelect
			{...props}
			noOptionsMessage={() => "Type to search"}
			cacheOptions={true}
			defaultOptions={false}
			loadOptions={sapOptions}
		/>
	)
}

export const SelectBrand = (props) => {
	const opts = brandsOptions.map(opt => {
		return (
			<option key={opt.id} value={opt.id}>{opt.brand_name}</option>
		);
	});
	return (
		<select className="form-control" {...props}>
			{opts}
		</select>
	)
}

export const SelectCategory = (props) => {
	return (
		<select className="form-control" {...props}>
			<option key="1" value="">--Category--</option>
			<option key="2" value="MFP">MFP</option>
			<option key="3" value="SFP">SFP</option>
		</select>
	)
}
export const SelectType = (props) => {
	return (
		<select className="form-control" {...props}>
			<option key="1" value="">--Type--</option>
			<option key="2" value="MONOCHROME">MONOCHROME</option>
			<option key="3" value="COLOR">COLOR</option>
		</select>
	)
}






