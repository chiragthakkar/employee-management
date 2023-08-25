FROM node:14-alpine

WORKDIR /var/www/html/src/react

# Copy package.json and package-lock.json for dependencies
COPY src/react/package.json ./
COPY src/react/package-lock.json ./
RUN npm install
COPY src/react/public/ ./public/
COPY src/react/src/ ./src/

EXPOSE 3000

CMD ["npm", "start"]
