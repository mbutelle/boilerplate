export default inputSchema => ({
  ...inputSchema,
  paths: Object.entries(inputSchema.paths).reduce(
    (acc, [path, pathItem]) => ({
      ...acc,
      [path.replace('_locale', 'locale')]: Object.entries(pathItem).reduce(
        (pathItemAcc, [verb, operation]) => ({
          ...pathItemAcc,
          [verb]: {
            ...operation,
            parameters: [
              ...(operation.parameters || []).map((parameter) => {
                parameter.name = parameter.name.replace('_locale', 'locale')
                return parameter
              }),
            ],
          },
        }),
        {},
      ),
    }),
    {},
  ),
})
